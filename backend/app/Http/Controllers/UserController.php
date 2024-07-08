<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyVerifiedException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\InvalidPreviousPasswordException;
use App\Exceptions\NonExistingUserException;
use App\Exceptions\NotRequestedPasswordChange;
use App\Exceptions\NotVerifiedEmailException;
use App\Exceptions\PasswordResetTokenExpired;
use App\Mail\RegistrationVerificationMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Mail;
use Validator;
use \DB;
use \DateTime;

class UserController extends Controller
{
    public function registration(Request $request){
        try{
            $this->validateRegistrationInput($request);
            $response = DB::transaction(function() use ($request){
                $ceatedUser = User::create([
                    'first_name'=>$request->firstName,
                    'last_name'=>$request->lastName,
                    'email'=>$request->email,
                    'password'=>bcrypt($request->password)
                ]);
        
                $verificationUrl = env('FRONT_END_URL') . '/email/verify/' . $ceatedUser->id;
                
                Mail::to($request->email)->send(new RegistrationVerificationMail($verificationUrl));

                return response()->json(['message'=>'The regsitration was successfull! Please verify yourself, we have sent you an email where you have to click the verify button!'], 202);
            });

            return $response;
        } catch(ValidationException $e){
            $errorMessages = $this->formatValidationErrorMessage($e);
            return response()->json(['error'=>$errorMessages], 422);
        }
    }

    public function verifyRegistration($userId){
        try{
            $verifiedUser = User::find($userId);
            if(!$verifiedUser){
                throw new NonExistingUserException();
            }

            $verification = $verifiedUser->email_verified_at;
            if($verification !== null){
                throw new AlreadyVerifiedException();
            }
            $verifiedUser->email_verified_at = Carbon::now();
            $verifiedUser->save();

            return response()->json(['message'=>'User successfully verified! Now you can log into your account!'], 200);
        } catch(NonExistingUserException | AlreadyVerifiedException $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }


    public function login(Request $request){
        try{
            $this->validateLoginInput($request);

            $user = User::where('email', $request->email)->first();

            if(!$user || !Hash::check($request->password, $user->password)){
                throw new InvalidCredentialsException();
            }

            if(!$user->email_verified_at){
                throw new NotVerifiedEmailException();
            }

            $authenticationTokenName = env('AUTH_KEY_NAME');
            $token = $user->createToken($authenticationTokenName)->plainTextToken;

            return response()->json([
                'id'=>$user->id,
                'firstName'=>$user->first_name,
                'lastName'=>$user->last_name,
                $authenticationTokenName=>$token
            ], 202);
        } catch(InvalidCredentialsException | NotVerifiedEmailException $e){
            return response()->json(['error'=>[$e->getMessage()]], $e->getCode());
        } catch(ValidationException $e){
            $errorMessages = $this->formatValidationErrorMessage($e);
            return response()->json(['error'=>$errorMessages], 422);            
        }
    }

    public function resetPasswordRequest(Request $request){
        try{
            $this->validateResetPasswordRequest($request);

            $result = DB::transaction(function () use ($request){
                $user = User::where(['email'=>$request->email])->first();

                if(!$user){
                    throw new NonExistingUserException();
                }

                $passwordResetToken = Uuid::uuid();

                $user->remember_token = $passwordResetToken;
                $user->password_reset_request_time_at = Carbon::now();
                $user->save();

                $resetPasswordUrl = env('FRONT_END_URL') . '/password/new/' . $passwordResetToken;

                Mail::to($user->email)->send(new ResetPasswordMail($resetPasswordUrl));
                return response()->json(['message'=>'Please check out your email inboxe! We have sent a password reset link!'], 200);
            });

            return $result;
        } catch(ValidationException $e){
            $errorMessages = $this->formatValidationErrorMessage($e);
            return response()->json(['error'=>$errorMessages], 422);
        } catch(NonExistingUserException $e){
            return response()->json(['error'=>[$e->getMessage()]], $e->getCode());
        }
    }

    public function verifyPasswordResetToken($passwordResetVerifyToken){
        try{
            $user = User::where('remember_token', $passwordResetVerifyToken)->first();

            if(!$user){
                throw new NotRequestedPasswordChange();
            }

            $this->validateResetToken($user);

            return response()->json(['status'=>'Accpeted'], 202);
        } catch(NotRequestedPasswordChange $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        } catch(PasswordResetTokenExpired $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }

    public function resetPassword(Request $request, $resetPasswordToken){
        try{
            $this->validateResetPassword($request);

            $user = User::where('remember_token', $resetPasswordToken)->first();

            if(!$user){
                throw new NotRequestedPasswordChange();
            }

            $this->validateResetToken($user);

            $user->password = bcrypt($request->password);
            $user->remember_token = null;
            $user->password_reset_request_time_at = null;
            $user->save();

            return response()->json(['message'=>'Password was successfully updated! Pleas try to login!'], 202);
        } catch(ValidationException $e){
            return response()->json(['error'=>$e->getMessage()], 422);
        } catch(NotRequestedPasswordChange $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }

    public function updateUser(Request $request){
        try{
            $this->validateUserUpdateInformationInputRequest($request);

            $user = $this->getUserFromBearerToken($request);
            if(!Hash::check($request->previousPassword, $user->password)){
                throw new InvalidPreviousPasswordException();
            }

            $user->first_name = $request->firstName;
            $user->last_name = $request->lastName;
            $user->email = $request->email;
            $user->password = bcrypt($request->newPassword);
            $user->save();

            return response()->json(['status'=>'User updated'], 202);
        } catch(ValidationException $e){
            $errorMessages = $this->formatValidationErrorMessage($e);
            return response()->json(['error'=>$errorMessages], 422);
        } catch(InvalidPreviousPasswordException $e){
            return response()->json(['error'=>[$e->getMessage()]], $e->getCode());
        }
    }

    public function getUserInformation(Request $request){
        $user = $this->getUserFromBearerToken($request);
        return response()->json(['user'=>[
            'id'=> $user->id,
            'firstName'=>$user->first_name,
            'lastName'=>$user->last_name,
            'email'=>$user->email,
        ]], 200);
    }

    protected function validateResetPassword(Request $request){
        return Validator::make(
            $request->all(),
            [
                'password'=>['required', 'min:6', 'max:255', 'confirmed']
            ],
            [
                'password.required'=>'Please fill out the password collumn!',
                'password.min'=>'Password should be at least 6 characters!',
                'password.max'=>'Password should be upmost 255 characters!',
                'password.confirmed'=>'Password and password confirmation are not matching!'
            ]
        )->validate();
    }

    protected function validateLoginInput(Request $request){
        return Validator::make(
            $request->all(),
            [
                'email'=>['required', 'email'],
                'password'=>['required', 'min:6', 'max:255']
            ],
            [
                'email.required'=>'Email is missing!',
                'email.email'=>'Invalid email format!',
                'password.required'=>'Password is missing!',
                'password.min'=>'The minimum length of password should be 6 characters!',
                'password.max'=>'The maximum length of password should be 255 characters!'
            ]
        )->validate();
    }

    protected function validateRegistrationInput(Request $request){
        return Validator::make(
            $request->all(), 
            [
                'firstName'=>['required', 'min:2', 'max:255'],
                'lastName'=>['required', 'min:2', 'max:255'],
                'email'=>['required', 'email'],
                'password'=>['required', 'confirmed', 'min:6', 'max:255'],
            ],
            [
                'firstName.min' => 'First name should be at least 2 characters!',
                'firstName.max' => 'First name shouldn\'t exceed 255 characters!',
                'lastName.min' => 'Last name should be at least 2 characters!',
                'lastName.max' => 'Last name shouldn\'t exceed 255 characters!',
                'email.email' => 'Email should be in email format!',
                'password.confirmed' => 'Password and password confirmation doesn\'t match!',
                'password.min' => 'Password should be at least 6 characters!',
                'password.max' => 'Password shouldn\'t exceed 255 characters!',
            ]
        )->validate();
    }

    protected function validateUserUpdateInformationInputRequest(Request $request){
        return Validator::make(
            $request->all(), 
            [
                'firstName'=>['required', 'min:2', 'max:255'],
                'lastName'=>['required', 'min:2', 'max:255'],
                'email'=>['required', 'email'],
                'previousPassword'=>['required', 'min:6', 'max:255'],
                'password'=>['required', 'confirmed', 'min:6', 'max:255'],
            ],
            [
                'firstName.min' => 'First name should be at least 2 characters!',
                'firstName.max' => 'First name shouldn\'t exceed 255 characters!',
                'lastName.min' => 'Last name should be at least 2 characters!',
                'lastName.max' => 'Last name shouldn\'t exceed 255 characters!',
                'email.email' => 'Email should be in email format!',
                'password.confirmed' => 'New password and password confirmation doesn\'t match!',
                'password.min' => 'New password should be at least 6 characters!',
                'password.max' => 'New password shouldn\'t exceed 255 characters!',
                'previousPassword.min' => 'New password should be at least 6 characters!',
                'previousPassword.max' => 'New password shouldn\'t exceed 255 characters!',
            ]
        );
    }

    protected function validateResetPasswordRequest(Request $request){
        return Validator::make(
            [
                'email'=>['required', 'email']
            ],
            [
                'email.required'=>'No email has been provided!',
                'email.email'=>'Invalid email format'
            ]
        )->validate();
    }

    protected function formatValidationErrorMessage(ValidationException $e){
        $errors = $e->errors();

        $errorMessages = [];
        foreach($errors as $error => $messages){
            foreach($messages as $message){
                $errorMessages[] = $message;
            }
        }

        return $errorMessages;
    }

    private function validateResetToken(User $user){
        $currTime = Carbon::now();
        $passwordRequestTime = Carbon::parse($user->password_reset_request_time_at);

        $timeDiff = intval($currTime->diff($passwordRequestTime)->format('%i'));

        if($timeDiff > 30){
            $this->clearPasswordResetToken($user);
            throw new PasswordResetTokenExpired();
        }
    }

    private function clearPasswordResetToken($user){
        $user->remember_token = null;
        $user->password_reset_request_time_at = null;
        $user->save();
    }

    private function getUserFromBearerToken(Request $request){
        $bearerToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($bearerToken);
        return $token->tokenable;
    }
}
