<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'account_name',
        'account_type',
        'balance',
        'is_account_blocked',
        'account_number',
    ];

    public function accountable(){
        return $this->morphTo();
    }
}
