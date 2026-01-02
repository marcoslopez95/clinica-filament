<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected static function booted(): void
    {
        static::created(function (Payment $payment) {
            $payment->invoice->updateStatusIfPaid();
        });

        static::updated(function (Payment $payment) {
            $payment->invoice->updateStatusIfPaid();
        });

        static::deleted(function (Payment $payment) {
            $payment->invoice->updateStatusIfPaid();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }
}
