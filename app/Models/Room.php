<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Models\Currency;

class Room extends Model
{
    use SoftDeletes;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
