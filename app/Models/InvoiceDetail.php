<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany as HasManyRelation;
use App\Models\ReferenceValueResult;

class InvoiceDetail extends Model
{
    use SoftDeletes;

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(InvoiceDetailTax::class);
    }

    public function referenceResults(): HasManyRelation
    {
        return $this->hasMany(ReferenceValueResult::class, 'invoice_detail_id');
    }

    public function batchDetails(): HasMany
    {
        return $this->hasMany(ProductBatchDetail::class);
    }
}
