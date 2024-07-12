<?php

namespace App;
use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
use App\Models\BaseAccount;
use App\Models\User;

trait AccountValidationTrait
{
    public function validateIfAccountExists(BaseAccount | null $account){
        if(!$account){
            throw new NonExistingAccount();
        }
    }

    public function validateIfUserHasPermissionForAccount(BaseAccount | null $account, User $user){
        if($account->user_id != $user->id){
            throw new ForbiddenAccountModification();
        }
    }
}
