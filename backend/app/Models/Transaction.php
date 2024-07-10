<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'transaction_time',
        'title',
        'description',
        'transaction_subcategory_id'
    ];

    public function transactionable(){
        return $this->morphTo();
    }
}
