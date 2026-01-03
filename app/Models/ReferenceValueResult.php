<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceValueResult extends Model
{
    protected $fillable = ['invoice_detail_id', 'reference_value_id', 'result'];

    public function invoiceDetail(): BelongsTo
    {
        return $this->belongsTo(InvoiceDetail::class);
    }

    public function referenceValue(): BelongsTo
    {
        return $this->belongsTo(ReferenceValue::class);
    }
}
