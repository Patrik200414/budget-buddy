<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavingsAccountRequest extends AccountRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'accountName'=>['required', 'min:3', 'max:255'],
            'balance'=>['required', 'numeric'],
            'accountNumber'=>['required', 'regex:/^[0-9]{9,18}$/'],
            'monthlyInterest'=>['required', 'numeric'],
            'monthlyMaintenanceFee'=>['required', 'numeric'],
            'transactionFee'=>['required', 'numeric'],
            'lastInterestPaiedAt'=>['date_format:Y-m-d'],
            'lastMonthlyFeePaidAt'=>['date_format:Y-m-d'],
            'minimumBalance'=>['required', 'numeric'],
            'maxAmountOfTransactionsMonthly'=>['required', 'numeric', 'max:30', 'min:0'],
            'lastAvaibleTransactionDate'=>['date_format:Y-m-d'],
            'limitExceedingFee'=>['required', 'numeric']
        ];
    }

    public function messages(): array{
        return [
            'accountName.required'=>'Account name is required!',
            'accountName.min'=>'Account should contain at least 3 characters!',
            'accountName.max'=>'Account should contain up most 255 characters!',
            'balance.required'=>'Balance is required!',
            'balance.numeric'=>'Balance should be a number!',
            'accountNumber.required'=>'Account number is required!',
            'accountNumber.regex'=>'Invalid account number format!',
            'monthlyInterest.required'=>'Monthly interest is required!',
            'monthlyInterest.numeric'=>'Monthly interest should be number!',
            'monthlyMaintenanceFee.required'=>'Monthly maintanance required!',
            'monthlyMaintenanceFee.numeric'=>'Monthly maintenance should be number!',
            'transactionFee.required'=>'Transaction fee is required!',
            'transactionFee'=>'Transaction fee should be number!',
            'lastInterestPaiedAt.date_format'=>'Last interest paid at should be date!',
            'lastMonthlyFeePaidAt.date_format'=>'Last interest paid at should be date!',
            'minimumBalance.required'=>'Minimum balance is required!',
            'minimumBalance.numeric'=>'Minimum balance should be number!',
            'maxAmountOfTransactionsMonthly.required'=>'Max amount of transactions monthly is required!',
            'maxAmountOfTransactionsMonthly.numeric'=>'Max amount of transactions monthly should be number!',
            'maxAmountOfTransactionsMonthly.max'=>'Max amount of transactions monthly shoud be up most 30!',
            'maxAmountOfTransactionsMonthly.min'=>'Max amount of transactions monthly should be at least 0!',
            'lastAvaibleTransactionDate.date_format'=>'Last avaibleTransaction date should be date!',
            'limitExceedingFee.required'=>'Limit exceeding fee is required!',
            'limitExceedingFee.numeric'=>'Limit exceeding fee should be number!'
        ];
    }
}
