<?php
namespace App\Handlers;
use App\MonthlyTransactionUtility;
use Carbon\Carbon;
use DB;

class RemoveFeeHandler extends Handler{
    use MonthlyTransactionUtility;

    public function handle(mixed &$request){
        DB::transaction(function() use($request){
            $lastmonthlyFeePaidAt = $request->accountable->last_monthly_fee_paid_at;
            $dateInfo = $this->getDateInformationsForMonthlyTransactions($request, $lastmonthlyFeePaidAt);
    
            if($dateInfo['timeDifference'] >= 1){
                $this->subtractMonthlyFee($request, $dateInfo['currentMonth'], $dateInfo['currentYear']);
            }
    
            if($this->nextHandler){
                $this->nextHandler->handle($request);
            }
        });
    }


    private function subtractMonthlyFee(mixed &$request, string $currMonth, string $currYear){
        $currBalance = $request->balance;
        $savingsAccount = $request->accountable;
        $balanceAfterRemoval = $currBalance - $savingsAccount->monthly_maintenance_fee;

        $request->balance = $balanceAfterRemoval;
        $savingsAccount->last_monthly_fee_paid_at = $this->getFirstDayOfMonth($currMonth, $currYear);

        $transactionSubcategoryType = $this->getTransactionSubcategory('Account Maintenance Fees');

        $feeTransaction = $this->createTransaction(
            $request,
            $balanceAfterRemoval,
            $currBalance,
            $currMonth,
            $currYear,
            $transactionSubcategoryType
        );

        $feeTransaction->save();
        $savingsAccount->save();
        $request->save();
    }
}