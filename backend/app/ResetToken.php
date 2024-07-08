<?php

namespace App;
use App\Exceptions\PasswordResetTokenExpired;
use App\Models\User;
use Carbon\Carbon;

trait ResetToken
{
    protected function validateResetToken(User $user){
        $currTime = Carbon::now();
        $passwordRequestTime = Carbon::parse($user->password_reset_request_time_at);

        $timeDiff = intval($currTime->diff($passwordRequestTime)->format('%i'));

        if($timeDiff > 30){
            $this->clearPasswordResetToken($user);
            throw new PasswordResetTokenExpired();
        }
    }

    protected function clearPasswordResetToken(User $user){
        $user->remember_token = null;
        $user->password_reset_request_time_at = null;
        $user->save();
    }
}
