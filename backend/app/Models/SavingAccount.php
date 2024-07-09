<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingAccount extends DepositAccount
{
    use HasFactory;

    protected $fillable = [
        'minimum_balance',
        'max_amount_of_transactions_monthly',
        'last_avaible_transaction_date',
        'limit_exceeding_fee'
    ];
}
