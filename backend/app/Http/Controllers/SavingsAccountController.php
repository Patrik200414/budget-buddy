<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\AccountValidationTrait;
use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
use App\Exceptions\UnableToDeleteAccountException;
use App\Models\BaseAccount;
use App\Models\SavingAccount;
use App\UserFromBearerToken;
use DB;
use App\Http\Requests\AccountRequest;
use \PDOException;

class SavingsAccountController extends AccountController
{
    use UserFromBearerToken, AccountValidationTrait;

    public function createAccount(AccountRequest $request){
        try{
            return DB::transaction(function() use($request){
                $user = $this->getUserFromBearerToken($request);
                $baseAccount = new BaseAccount();
                $savingsAccount = new SavingAccount();
                $baseAccount->user_id = $user->id;
                $baseAccount->account_type = AccountType::SAVINGS_ACCOUNT->value;

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
    
    public function updateAccount(AccountRequest $request, string $accountId){
        try{
            return DB::transaction(function() use($request, $accountId){
                $user = $this->getUserFromBearerToken($request);

                $baseAccount = BaseAccount::with('accountable')->where(['id'=>$accountId])->first();

                if($baseAccount->account_type === AccountType::HOLDINGS_ACCOUNT){
                    throw new UnableToDeleteAccountException();
                }
                
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
            return response()->json(['errors'=> ['error'=>['An account with this account number is already exists!']]], 409);
        }
    }
}
