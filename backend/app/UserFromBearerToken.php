<?php

namespace App;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;;

trait UserFromBearerToken
{
    public function getUserFromBearerToken(Request $request){
        $bearerToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($bearerToken);
        return $token->tokenable;
    }
}
