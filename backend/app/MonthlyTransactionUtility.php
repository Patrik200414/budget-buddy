<?php

namespace App;
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

        /* $lastInterestPaidAt = Carbon::parse($request->accountable->last_interest_paied_at); */
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
}
