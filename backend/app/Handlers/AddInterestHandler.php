<?php
namespace App\Handlers;

use App\Exceptions\NonExistingTransactionSubcategoryException;
use App\Models\Transaction;
use App\Models\TransactionSubcategory;
use App\MonthlyTransactionUtility;
use Carbon\Carbon;
use DB;
class AddInterestHandler extends Handler {   
    
    use MonthlyTransactionUtility;

    public function handle(mixed &$request){
        DB::transaction(function() use(&$request){
            $lastInterestPaidAt = $request->accountable->last_interest_paied_at;
            $dateInfo = $this->getDateInformationsForMonthlyTransactions($request, $lastInterestPaidAt);
    
            if($dateInfo['timeDifference'] >= 1){
                $this->makeIncomTransaction($request, $dateInfo['timeDifference'], $dateInfo['currentMonth'], $dateInfo['currentYear']);
            }
    
            if($this->nextHandler){
                $this->nextHandler->handle($request);
            }
        });
    }


    private function makeIncomTransaction(mixed $request, int $timeDiff, string $currMonth, string $currYear){
        $currBalance = $request->balance;
        $savingsAccount = $request->accountable;
        $income = $currBalance * ((1 + $savingsAccount->monthly_interest / 100) * $timeDiff);
        $request->balance = $income;
        $savingsAccount->last_interest_paied_at = $this->getFirstDayOfMonth($currMonth, $currYear);

        $transactionSubcategoryType = $this->getTransactionSubcategory('Investment income');

        $incomeTransaction = $this->createTransaction(
            $request,
            $income,
            $currBalance,
            $currMonth,
            $currYear,
            $transactionSubcategoryType
        );

        $incomeTransaction->save();
        $savingsAccount->save();
        $request->save();
    }

    
}