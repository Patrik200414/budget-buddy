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


    private function createTransaction(
        mixed $request,
        float $income,
        float $currBalance, 
        string $currMonth, 
        string $currYear,
        TransactionSubcategory $transactionSubcategoryType
    ){
        $incomeTransaction = new Transaction();
        $incomeTransaction->account_id = $request->id;
        $incomeTransaction->amount = $income - $currBalance;
        $incomeTransaction->transaction_time = $this->getFirstDayOfMonth($currMonth, $currYear);
        $incomeTransaction->title = 'Savings income';
        $incomeTransaction->description = 'This is the monthly income of the savings account.';
        $incomeTransaction->transaction_subcategory_id = $transactionSubcategoryType->id;

        return $incomeTransaction;
    }

    private function makeIncomTransaction(mixed $request, int $timeDiff, string $currMonth, string $currYear){
        $currBalance = $request->balance;
        $savingsAccount = $request->accountable;
        $income = $currBalance * ((1 + $savingsAccount->monthly_interest / 100) * $timeDiff);
        $request->balance = $income;
        $savingsAccount->last_interest_paied_at = $this->getFirstDayOfMonth($currMonth, $currYear);

        $transactionSubcategoryType = TransactionSubcategory::where(['transaction_subcategory_name'=>'Investment income'])->first();
        if(!$transactionSubcategoryType){
            throw new NonExistingTransactionSubcategoryException();
        }

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