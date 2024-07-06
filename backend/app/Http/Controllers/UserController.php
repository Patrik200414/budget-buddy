<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyVerifiedException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\NonExistingUserException;
use App\Exceptions\NotVerifiedEmailException;
use App\Mail\RegistrationVerificationMail;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mail;
use Validator;
use \DB;

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
        
                $verificationUrl = env('EMAIL_VERIFICATION_LINK');
                
                Mail::to($request->email)->send(new RegistrationVerificationMail($verificationUrl . '/' . $ceatedUser->id));

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
            $verifiedUser->email_verified_at = now();
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
}
