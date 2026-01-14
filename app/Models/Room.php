<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Models\Currency;

class Room extends Model implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
