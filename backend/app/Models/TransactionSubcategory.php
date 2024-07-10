<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_subcategory_name',
        'transaction_category_id'
    ];
}
