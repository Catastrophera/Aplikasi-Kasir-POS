<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['channel', 'payment_method', 'total_price'];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
