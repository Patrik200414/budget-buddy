<?php

namespace App\Http\Controllers;

use App\Models\SavingAccount;
use App\UserFromBearerToken;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;

class SavingsAccountController extends AccountController
{
    use UserFromBearerToken;

    public function createAccount(AccountRequest $request){
        $user = $this->getUserFromBearerToken($request);

        $account = new SavingAccount();
        $account->user_id = $user->id;
        $account->account_name= $request->accountName;
        $account->balance = $request->balance;
        $account->is_account_blocked = false;
        $account->account_number = $request->accountNumber;
        $account->monthly_interest = $request->monthlyInterest;
        $account->monthly_maintenance_fee = $request->monthlyMaintenanceFee;
        $account->transaction_fee = $request->transactionFee;
        $account->last_interest_paied_at = $request->lastInterestPaiedAt;
        $account->last_monthly_fee_paid_at = $request->lastMonthlyFeePaidAt; 
        $account->minimum_balance = $request->minimumBalance;
        $account->max_amount_of_transactions_monthly = $request->maxAmountOfTransactionsMonthly;
        $account->last_avaible_transaction_date = $request->lastAvaibleTransactionDate;
        $account->limit_exceeding_fee = $request->limitExceedingFee;

        $account->save();
        return response()->json(['status'=>'Account successfully created!'], 202);
    }
    public function deleteAccount(string $accountId){

    }
    public function updateAccount(AccountRequest $request){

    }
    public function blockAccount(string $accountId){

    }
    public function getAccount($useId, $accountId){

    }
}
