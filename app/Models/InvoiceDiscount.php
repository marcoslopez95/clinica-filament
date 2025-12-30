<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDiscount extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::created(function (InvoiceDiscount $discount) {
            $discount->invoice->updateStatusIfPaid();
        });

        static::updated(function (InvoiceDiscount $discount) {
            $discount->invoice->updateStatusIfPaid();
        });

        static::deleted(function (InvoiceDiscount $discount) {
            $discount->invoice->updateStatusIfPaid();
        });
    }

    protected $fillable = [
        'invoice_id',
        'amount',
        'percentage',
        'description',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
