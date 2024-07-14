<?php

namespace App;
use App\Models\BaseAccount;
use App\Models\HoldingAccount;
use App\Models\User;

trait CreateHoldingsAccount
{
    public function saveHoldingsAccount(User $user, float $deletableBalance, int $minHoldingsAccountNumber, int $maxHoldingsAccountNumber){
        $holdingsAccountNumber = $this->generateHoldingAccountNumber($minHoldingsAccountNumber, $maxHoldingsAccountNumber);
        $holdingAccountByUser = BaseAccount::where(['account_type'=>AccountType::HOLDINGS_ACCOUNT->value, 'user_id'=>$user->id])->first();
        $this->createHoldingsAccount($user, $deletableBalance, $holdingsAccountNumber, $holdingAccountByUser);
    }

    /**
     * This method is creating a holdings account if the user wants to delete one of there accounts to hold there balance there.
     * It required transaction! Without transaction it can break the database structure!
     * @param \App\Models\User $user
     * @param float $balance
     * @param string $accountNumber
     * @return void
     */
    private function createHoldingsAccount(User $user, float $balance, string $accountNumber, $baseAccount){
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


    private function generateHoldingAccountNumber(int $minHoldingsAccountNumber, int $maxHoldingsAccountNumber){
        $lastCreatedHoldingAccount = BaseAccount::where(['account_type'=>AccountType::HOLDINGS_ACCOUNT->value])->orderBy('created_at', 'desc')->first();
        $holdingsAccountNumber = null;

        if($lastCreatedHoldingAccount){
            $holdingsAccountNumber = $this->generateHoldingAccountNumberfromPreviousHoldingAccount($lastCreatedHoldingAccount);
        } else{
            $holdingsAccountNumber = $this->generateNewHoldingAccountNumber($minHoldingsAccountNumber, $maxHoldingsAccountNumber);
        }

        return $holdingsAccountNumber;
    }

    private function generateHoldingAccountNumberfromPreviousHoldingAccount(BaseAccount $lastCreatedHoldingAccount){
        $lastRecordedHoldingsAccountNumber = $lastCreatedHoldingAccount->account_number;
        $separatedHoldingsAccountNumber = explode('-', $lastRecordedHoldingsAccountNumber);
        $separatedHoldingsAccountNumber[0] = intval($separatedHoldingsAccountNumber[0]) + 1;

        return $separatedHoldingsAccountNumber[0] . '-' . $separatedHoldingsAccountNumber[1];
    }

    /**
     * 
     * 
     * change static values
     */
    private function generateNewHoldingAccountNumber(int $minHoldingsAccountNumber, int $maxHoldingsAccountNumber){
        $generatedAccountNumber = (string) rand($minHoldingsAccountNumber, $maxHoldingsAccountNumber);
        return $generatedAccountNumber . '-H';
    }
}
