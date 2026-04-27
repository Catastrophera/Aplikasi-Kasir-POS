<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'cash_drawer_id', 'opened_by', 'opening_balance',
        'closing_balance', 'total_sales', 'total_transactions',
        'opened_at', 'closed_at', 'status', 'notes'
    ];

    protected $casts = [
        'opened_at'       => 'datetime',
        'closed_at'       => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales'     => 'decimal:2',
    ];

    public function cashDrawer()
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function getDurationAttribute(): string
    {
        $start = $this->opened_at;
        $end   = $this->closed_at ?? now();
        $mins  = $start->diffInMinutes($end);
        $h     = intdiv($mins, 60);
        $m     = $mins % 60;
        return $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
    }
}
