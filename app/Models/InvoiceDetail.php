<?php

namespace App\Models;

use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(InvoiceDetailTax::class);
    }

    public function batchDetails(): HasMany
    {
        return $this->hasMany(ProductBatchDetail::class);
    }

    protected static function booted(): void
    {
        static::created(function (InvoiceDetail $detail) {
            $amount = $detail->product->inventory->amount;
            $quantity = $detail->quantity;

            if ($detail->invoice->invoice_type === InvoiceType::INVENTORY->value) {
                $detail->product->inventory->update(['amount' => $amount + $quantity]);
            } else {
                $detail->product->inventory->update(['amount' => $amount - $quantity]);
            }
        });

        static::updating(function (InvoiceDetail $detail) {
            $oldQuantity = $detail->getOriginal('quantity');
            $newQuantity = +$detail->quantity;
            $diff = $newQuantity - $oldQuantity;
            $amountInInventory = $detail->product->inventory->amount;

            if ($detail->invoice->invoice_type === InvoiceType::INVENTORY->value) {
                $detail->product->inventory->update(['amount' => $amountInInventory + $diff]);
            } else {
                $detail->product->inventory->update(['amount' => $amountInInventory - $diff]);
            }
        });

        static::deleted(function (InvoiceDetail $detail) {
            $amount = $detail->product->inventory->amount;
            $quantity = $detail->quantity;

            if ($detail->invoice->invoice_type === InvoiceType::INVENTORY->value) {
                $detail->product->inventory->update(['amount' => $amount - $quantity]);
            } else {
                $detail->product->inventory->update(['amount' => $amount + $quantity]);
            }
        });
    }
}
