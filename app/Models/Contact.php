<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'type'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
