<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
use App\Exceptions\NonExistingUserException;
use App\Models\BaseAccount;
use App\Models\SavingAccount;
use App\Models\User;
use App\UserFromBearerToken;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use \PDOException;

class SavingsAccountController extends AccountController
{
    use UserFromBearerToken;

    public function createAccount(AccountRequest $request){
        try{
            $user = $this->getUserFromBearerToken($request);
            $account = new SavingAccount();

            $account->user_id = $user->id;

            $updatedAccount = $this->setAccountInformations($request, $account);
            $updatedAccount->save();
            return response()->json(['status'=>'Account successfully created!'], 202);
        } catch(PDOException $e){
            
        }
    }
    public function deleteAccount(string $accountId){
    }
    public function updateAccount(AccountRequest $request, string $accountId){
        try{
            $user = $this->getUserFromBearerToken($request);

            $account = SavingAccount::where(['id'=>$accountId])->first();
            if(!$account){
                throw new NonExistingAccount();
            }

            if(!$user || $account->user_id != $user->id){
                throw new ForbiddenAccountModification();
            }

            $updatedAccount = $this->setAccountInformations($request, $account);
            $updatedAccount->save();

            return response()->json(['status'=>'Account successfully updated!'], 202);
        } catch(NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        } catch(PDOException $e){
            return $this->handlePDOExceptions($e);
        }
    }
    public function blockAccount(Request $request, string $accountId){
        try{
            $user = $this->getUserFromBearerToken($request);
            $account = SavingAccount::where(['id'=>$accountId])->first();

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


    private function setAccountInformations(AccountRequest $request, SavingAccount $account){        
        $account->account_name= $request->accountName;
        $account->balance = $request->balance;
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

        return $account;
    }

    private function handlePDOExceptions(PDOException $e){
        $exceptionCode = $e->errorInfo[0];
        if($exceptionCode === '23000'){
            return response()->json(['error'=> 'An account with this account number is already exists!'], 409);
        }
    }

    private function validateIfAccountExists(SavingAccount $account){
        if(!$account){
            throw new NonExistingAccount();
        }
    }

    private function validateIfUserHasPermissionForAccount(SavingAccount $account, User $user){
        if($account->user_id != $user->id){
            throw new ForbiddenAccountModification();
        }
    }
}
