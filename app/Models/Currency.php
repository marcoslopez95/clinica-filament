<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Models\Exam;

class Currency extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;

    protected $casts = [
        'is_main' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($currency) {
            if ($currency->is_main) {
                // Forzar tasa de cambio a 1 si es la moneda principal
                $currency->exchange = 1;

                // Desmarcar otras monedas como principales
                static::where('id', '!=', $currency->id)->update(['is_main' => false]);
            }
        });
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
