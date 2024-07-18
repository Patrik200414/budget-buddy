<?php

namespace App;
use App\Exceptions\NonExistingTransactionSubcategoryException;
use App\Models\Transaction;
use App\Models\TransactionSubcategory;
use Carbon\Carbon;

trait MonthlyTransactionUtility
{
    public function getFirstDayOfMonth(string $currMonth, string $currYear){
        return date('Y-m-d H:i:s', mktime(0, 0, 0, $currMonth, 1, $currYear));
    }

    public function getDateInformationsForMonthlyTransactions(mixed $request, string $monthlyPayment){
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;

        $lastMontlyPayment = Carbon::parse($monthlyPayment);
        
        $timeDiff = $lastMontlyPayment->diffInMonths($currentDate);

        return [
            'currentDate'=>$currentDate,
            'currentMonth'=>$currentMonth,
            'currentYear'=>$currentYear,
            'lastMonthlypayment'=>$lastMontlyPayment,
            'timeDifference'=>$timeDiff
        ];
    }

    public function getTransactionSubcategory(string $transactionSubcategoryName){
        $transactionSubcategoryType = TransactionSubcategory::where(['transaction_subcategory_name'=>$transactionSubcategoryName])->first();
        if(!$transactionSubcategoryType){
            throw new NonExistingTransactionSubcategoryException();
        }

        return $transactionSubcategoryType;
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
}
