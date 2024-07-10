<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_name',
        'balance',
        'is_account_blocked',
        'account_number',
        'is_deletable'
    ];

    public function transactions(){
        return $this->morphMany('App\Transaction', 'transactionable');
    }
}