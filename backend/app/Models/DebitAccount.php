<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitAccount extends DepositAccount
{
    use HasFactory;

    protected $fillable = [
        'overdraft_fee'
    ];

    public function account(){
        return $this->morphOne(BaseAccount::class, 'accountable');
    }
}
