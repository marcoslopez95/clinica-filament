<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'payment_id',
        'payment_method_id',
        'currency_id',
        'exchange',
    ];

    protected static function booted(): void
    {
        static::created(function (Refund $refund) {
            $refund->payment->invoice->updateStatusIfPaid();
        });

        static::updated(function (Refund $refund) {
            $refund->payment->invoice->updateStatusIfPaid();
        });

        static::deleted(function (Refund $refund) {
            $refund->payment->invoice->updateStatusIfPaid();
        });
    }

    public function invoice()
    {
        return $this->payment->invoice();
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
