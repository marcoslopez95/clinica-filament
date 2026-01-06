<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetailTax extends Model
{
    use SoftDeletes;

    public function invoiceDetail(): BelongsTo
    {
        return $this->belongsTo(InvoiceDetail::class);
    }
}
