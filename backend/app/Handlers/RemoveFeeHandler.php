<?php
namespace App\Handlers;
use App\MonthlyTransactionUtility;
use Carbon\Carbon;
use DB;

class RemoveFeeHandler extends Handler{
    use MonthlyTransactionUtility;

    public function handle(mixed &$request){
        DB::transaction(function() use($request){
            $savingsAccount = $request->accountable;
            
    
            $this->calculateLimitExceedingFee($request, $savingsAccount);

            $feeTransaction = $this->subtractMonthlyFee($request, $savingsAccount);

            if($feeTransaction){
                $feeTransaction->save();
                $savingsAccount->save();
                $request->save();
            }
    
            if($this->nextHandler){
                $this->nextHandler->handle($request);
            }
        });
    }


    private function subtractMonthlyFee(mixed &$request, mixed $savingsAccount){
        $lastmonthlyFeePaidAt = $request->accountable->last_monthly_fee_paid_at;
        $dateInfo = $this->getDateInformationsForMonthlyTransactions($request, $lastmonthlyFeePaidAt);

        $feeTransaction = null;
        if($dateInfo['timeDifference'] >= 1){
            $currBalance = $request->balance;
            $balanceAfterRemoval = $currBalance - $savingsAccount->monthly_maintenance_fee;
    
            $request->balance = $balanceAfterRemoval;
            $savingsAccount->last_monthly_fee_paid_at = $this->getFirstDayOfMonth($dateInfo['currentMonth'], $dateInfo['currentYear']);
    
            $transactionSubcategoryType = $this->getTransactionSubcategory('Account Maintenance Fees');
    
            $feeTransaction = $this->createTransaction(
                $request,
                $balanceAfterRemoval,
                $currBalance,
                $dateInfo['currentMonth'],
                $dateInfo['currentYear'],
                $transactionSubcategoryType
            );
        }

        return $feeTransaction;
    }

    private function calculateLimitExceedingFee(mixed &$request, mixed &$savingsAccount){
        $accountExceededMinBalanceAt = $savingsAccount->accountaccount_exceeded_min_blance_at;

        $balanceExceedingFeePayments = 0;
        if($accountExceededMinBalanceAt){
            $dateInfo = $this->getDateInformationsForMonthlyTransactions($request, $accountExceededMinBalanceAt);
            
            if($dateInfo['timeDifference'] >= 1){
                $balanceExceedingFeePayments = $savingsAccount->account_exceeded_min_blance_at * floor($dateInfo['timeDifference']);
                $request->balance -= $balanceExceedingFeePayments;        
            }
        }

        if($request->balance < $savingsAccount->minimum_balance){
            $savingsAccount->account_exceeded_min_blance_at = $this->getFirstDayOfMonth($dateInfo['currentMonth'], $dateInfo['currentYear']);
        }
    }
}