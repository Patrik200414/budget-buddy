<?php

namespace App;
use App\Models\BaseAccount;
use App\Models\HoldingAccount;
use App\Models\User;

trait CreateHoldingsAccount
{
    /**
     * This method is creating a holdings account if the user wants to delete one of there accounts to hold there balance there.
     * It required transaction! Without transaction it can break the database structure!
     * @param \App\Models\User $user
     * @param float $balance
     * @param string $accountNumber
     * @return void
     */
    public function createHoldingsAccount(User $user, float $balance, string $accountNumber, $baseAccount){
        if(!$baseAccount){
            $holdingAccount = new HoldingAccount();
            $holdingAccount->save();
    
            $baseAccount = new BaseAccount();
            $baseAccount->accountable()->associate($holdingAccount);
        }
        
        $baseAccount->user_id = $user->id;
        $baseAccount->account_name = 'Holdings account';
        $baseAccount->account_type = AccountType::HOLDINGS_ACCOUNT->value;
        $baseAccount->balance += $balance;
        $baseAccount->account_number = $accountNumber;
        $baseAccount->save();
    }
}
