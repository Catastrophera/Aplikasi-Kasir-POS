<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowEntry extends Model
{
    protected $fillable = ['type', 'amount', 'description', 'date', 'shift_id', 'created_by'];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Scope helpers
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
