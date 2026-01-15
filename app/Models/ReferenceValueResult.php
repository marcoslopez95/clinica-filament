<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceValueResult extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
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
