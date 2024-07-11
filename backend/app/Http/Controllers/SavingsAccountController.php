<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
use App\Exceptions\NonExistingUserException;
use App\Models\BaseAccount;
use App\Models\SavingAccount;
use App\Models\User;
use App\UserFromBearerToken;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use \PDOException;

class SavingsAccountController extends AccountController
{
    use UserFromBearerToken;

    public function createAccount(AccountRequest $request){
        try{
            return DB::transaction(function() use($request){
                $user = $this->getUserFromBearerToken($request);
                $baseAccount = new BaseAccount();
                $savingsAccount = new SavingAccount();
                $baseAccount->user_id = $user->id;

                $this->saveAccountInformations($request, $baseAccount, $savingsAccount);
                $savingsAccount->save();
                $baseAccount->accountable()->associate($savingsAccount);
                $baseAccount->save();
                
                return response()->json(['status'=>'Account successfully created!'], 202);
            });
        } catch(PDOException $e){
            return $this->handlePDOExceptions($e);
        }
    }
    public function deleteAccount(string $accountId){
    }
    public function updateAccount(AccountRequest $request, string $accountId){
        try{
            return DB::transaction(function() use($request, $accountId){
                $user = $this->getUserFromBearerToken($request);

                $baseAccount = BaseAccount::with('accountable')->where(['id'=>$accountId])->first();
                
                $this->validateIfAccountExists($baseAccount);
                $this->validateIfUserHasPermissionForAccount($baseAccount, $user);
                
                $savingsAccount = $baseAccount->accountable;
                
                $this->saveAccountInformations($request, $baseAccount, $savingsAccount);
                
                $baseAccount->save();
                $savingsAccount->save();
    
                return response()->json(['status'=>'Account successfully updated!'], 202);
            });
        } catch(NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        } catch(PDOException $e){
            return $this->handlePDOExceptions($e);
        }
    }
    public function blockAccount(Request $request, string $accountId){
        try{
            $user = $this->getUserFromBearerToken($request);
            $account = BaseAccount::where(['id'=>$accountId])->first();

            $this->validateIfAccountExists($account);
            $this->validateIfUserHasPermissionForAccount($account, $user);

            $account->is_account_blocked = true;
            $account->save();

            return response()->json(['status'=>'Account is successfully blocked!'], 202);
        } catch(NonExistingUserException | NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }
    public function getAccount(string $accountId){

    }


    private function saveAccountInformations(AccountRequest $request, BaseAccount $baseAccount, SavingAccount $savingsAccount){        
        $savingsAccount->monthly_interest = $request->monthlyInterest;
        $savingsAccount->monthly_maintenance_fee = $request->monthlyMaintenanceFee;
        $savingsAccount->transaction_fee = $request->transactionFee;
        $savingsAccount->last_interest_paied_at = $request->lastInterestPaiedAt;
        $savingsAccount->last_monthly_fee_paid_at = $request->lastMonthlyFeePaidAt; 
        $savingsAccount->minimum_balance = $request->minimumBalance;
        $savingsAccount->max_amount_of_transactions_monthly = $request->maxAmountOfTransactionsMonthly;
        $savingsAccount->last_avaible_transaction_date = $request->lastAvaibleTransactionDate;
        $savingsAccount->limit_exceeding_fee = $request->limitExceedingFee;
        
        $baseAccount->account_name = $request->accountName;
        $baseAccount->balance = $request->balance;
        $baseAccount->account_number = $request->accountNumber;
    }

    private function handlePDOExceptions(PDOException $e){
        $exceptionCode = $e->errorInfo[0];
        if($exceptionCode === '23000'){
            return response()->json(['error'=> 'An account with this account number is already exists!'], 409);
        }
    }

    private function validateIfAccountExists(BaseAccount $account){
        if(!$account){
            throw new NonExistingAccount();
        }
    }

    private function validateIfUserHasPermissionForAccount(BaseAccount $account, User $user){
        if($account->user_id != $user->id){
            throw new ForbiddenAccountModification();
        }
    }
}
