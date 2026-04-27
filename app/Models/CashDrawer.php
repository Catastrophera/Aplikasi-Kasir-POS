<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashDrawer extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function activeShift()
    {
        return $this->hasOne(Shift::class)->where('status', 'open')->latest();
    }
}
