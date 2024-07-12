<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\AccountValidationTrait;
use App\CreateHoldingsAccount;
use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
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
    use UserFromBearerToken, CreateHoldingsAccount, AccountValidationTrait;

    const MIN_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER = 100000000;
    const MAX_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER = 999999999999999;

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
    public function deleteAccount(Request $request, string $accountId){
        try{
            return DB::transaction(function() use($request, $accountId) {
                $user = $this->getUserFromBearerToken($request);
                $deletableBaseAccount = BaseAccount::with('accountable')->where(['id'=>$accountId])->where(['account_type'=>AccountType::SAVINGS_ACCOUNT->value])->first();
                $this->validateIfAccountExists($deletableBaseAccount);
                $this->validateIfUserHasPermissionForAccount($deletableBaseAccount, $user);
                $deletableBalance = $deletableBaseAccount->balance;
    
                $transferAccountId = $request->transferAccountId;
    
                if($transferAccountId){
                    $this->transferBalanceToOtherAccount($transferAccountId, $user, $deletableBalance);
                } else {
                    $this->saveHoldingsAccount($user, $deletableBalance);
                }
    
                $deletableSavingsAccountId = $deletableBaseAccount->accountable_id;
    
                var_dump($deletableSavingsAccountId);
                $deletableBaseAccount->delete();
                SavingAccount::where(['id'=>$deletableSavingsAccountId])->delete();

                return response()->json(['message'=>'Deletion was successfull!'], 200);
            });

        } catch(NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
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

    public function getAccount(string $accountId){

    }

    private function transferBalanceToOtherAccount(string $transferAccountId, User $user, float $deletableBalance){
        $transferAccount = BaseAccount::with('accountable')->where(['id'=>$transferAccountId])->first();
        $this->validateIfAccountExists($transferAccount);
        $this->validateIfUserHasPermissionForAccount($transferAccount, $user);

        $transferAccount->balance += $deletableBalance;
        $transferAccount->save();
    }

    private function generateHoldingAccountNumber(){
        $lastCreatedHoldingAccount = BaseAccount::where(['account_type'=>AccountType::HOLDINGS_ACCOUNT->value])->orderBy('created_at', 'desc')->first();
        $holdingsAccountNumber = null;

        if($lastCreatedHoldingAccount){
            $holdingsAccountNumber = $this->generateHoldingAccountNumberfromPreviousHoldingAccount($lastCreatedHoldingAccount);
        } else{
            $holdingsAccountNumber = $this->generateNewHoldingAccountNumber();
        }

        return $holdingsAccountNumber;
    }

    private function saveHoldingsAccount(User $user, float $deletableBalance){
        $holdingsAccountNumber = $this->generateHoldingAccountNumber();
        $holdingAccountByUser = BaseAccount::where(['account_type'=>AccountType::HOLDINGS_ACCOUNT->value, 'user_id'=>$user->id])->first();
        $this->createHoldingsAccount($user, $deletableBalance, $holdingsAccountNumber, $holdingAccountByUser);
    }

    private function generateNewHoldingAccountNumber(){
        $generatedAccountNumber = (string) rand(self::MIN_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER, self::MAX_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER);
        return $generatedAccountNumber . '-H';
    }

    private function generateHoldingAccountNumberfromPreviousHoldingAccount(BaseAccount $lastCreatedHoldingAccount){
        $lastRecordedHoldingsAccountNumber = $lastCreatedHoldingAccount->account_number;
        $separatedHoldingsAccountNumber = explode('-', $lastRecordedHoldingsAccountNumber);
        $separatedHoldingsAccountNumber[0] = intval($separatedHoldingsAccountNumber[0]) + 1;

        return $separatedHoldingsAccountNumber[0] . '-' . $separatedHoldingsAccountNumber[1];
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
}
