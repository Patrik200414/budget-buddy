<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingAccount extends DepositAccount
{
    use HasFactory, HasUuids;

    public $table = 'savings_accounts';

    protected $fillable = [
        'minimum_balance',
        'max_amount_of_transactions_monthly',
        'last_avaible_transaction_date',
        'limit_exceeding_fee'
    ];

    public function account(){
        return $this->morphOne(BaseAccount::class, 'accountable');
    }

    public function name(){
        return 'Savings account';
    }
}
