<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyVerifiedException;
use App\Exceptions\NonExistingUserException;
use App\Mail\RegistrationVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mail;
use \Exception;

class UserController extends Controller
{
    public function registration(Request $request){
        try{
            $request->validate(
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
            );

            $userId = uuid_create();
            User::create([
                'id'=>$userId,
                'first_name'=>$request->firstName,
                'last_name'=>$request->lastName,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);

            
    
            $verificationUrl = env('EMAIL_VERIFICATION_LINK');
            
            Mail::to($request->email)->send(new RegistrationVerificationMail($verificationUrl . '/' . $userId));

            return response()->json(['message'=>'The regsitration was successfull! Please verify yourself, we have sent you an email where you have to click the verify button!'], 202);
        } catch(ValidationException $e){
            return response()->json($e->validator->errors(), 422);
        }    
    }

    public function verifyRegistration($userId){
        try{
            $verifiedUser = User::find($userId);
            if($verifiedUser === null){
                throw new NonExistingUserException();
            }

            $verification = $verifiedUser->email_verified_at;
            if($verification !== null){
                throw new AlreadyVerifiedException();
            }
            $verifiedUser->email_verified_at = time();
            $verifiedUser->save();

            return response(null, 200);
        } catch(NonExistingUserException | AlreadyVerifiedException $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }
}
