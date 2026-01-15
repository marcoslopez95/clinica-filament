<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;

    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class);
    }
}
