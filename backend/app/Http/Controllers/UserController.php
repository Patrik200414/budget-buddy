<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyVerifiedException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\InvalidPreviousPasswordException;
use App\Exceptions\NonExistingUserException;
use App\Exceptions\NotRequestedPasswordChange;
use App\Exceptions\NotVerifiedEmailException;
use App\Exceptions\PasswordResetTokenExpired;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ResetPasswordValidationRequest;
use App\Http\Requests\UserUpdateInformationRequest;
use App\Mail\RegistrationVerificationMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\ResetToken;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Hash;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Mail;
use \DB;

class UserController extends Controller
{
    use ResetToken;

    public function registration(RegistrationRequest $request){
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
            return response()->json(['errors'=>$e->getMessage()], $e->getCode());
        }
    }


    public function login(LoginRequest $request){
        try{
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
                $authenticationTokenName=>$token,
                'email'=>$user->email
            ], 202);
        } catch(InvalidCredentialsException | NotVerifiedEmailException $e){
            return response()->json(['errors'=>[
                'message'=>[$e->getMessage()]
            ]], $e->getCode());
        }
    }

    public function resetPasswordRequest(ResetPasswordRequest $request){
        try{
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
        } catch(NonExistingUserException $e){
            return response()->json(['errors'=>[$e->getMessage()]], $e->getCode());
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
        } catch(NotRequestedPasswordChange | PasswordResetTokenExpired $e){
            return response()->json(['errors'=>$e->getMessage()], $e->getCode());
        }
    }

    public function resetPassword(ResetPasswordValidationRequest $request, $resetPasswordToken){
        try{
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
        } catch(NotRequestedPasswordChange $e){
            return response()->json(['errors'=>$e->getMessage()], $e->getCode());
        }
    }

    public function updateUser(UserUpdateInformationRequest $request){
        try{
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
        } catch(InvalidPreviousPasswordException $e){
            return response()->json(['errors'=>[$e->getMessage()]], $e->getCode());
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


    private function getUserFromBearerToken(Request $request){
        $bearerToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($bearerToken);
        return $token->tokenable;
    }
}
