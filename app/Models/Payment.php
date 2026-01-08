<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    
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
        // Refunds were migrated to reference `invoice_id` instead of `payment_id`.
        // Match refunds by the payment's `invoice_id`.
        return $this->hasOne(Refund::class, 'invoice_id', 'invoice_id');
    }
}
