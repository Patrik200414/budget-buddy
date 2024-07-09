<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class DepositAccount extends BaseAccount
{
    use HasFactory;

    protected $fillable = [
        'monthly_interest',
        'monthly_maintenance_fee',
        'transaction_fee',
        'last_interest_paied_at',
        'last_monthly_fee_paid_at'
    ];
}
