<?php

namespace App\Models;

use App\Enums\InvoiceType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function batchDetails(): HasMany
    {
        return $this->hasMany(ProductBatchDetail::class);
    }

    protected static function booted(): void
    {
        static::created(function (InvoiceDetail $detail) {
            $item = $detail->content ?? $detail->product;

            if ($item instanceof Product && $item->inventory) {
                $amount = $item->inventory->amount;
                $quantity = $detail->quantity;

                if ($detail->invoice->invoice_type === InvoiceType::INVENTORY->value) {
                    $item->inventory->update(['amount' => $amount + $quantity]);
                } else {
                    $item->inventory->update(['amount' => $amount - $quantity]);
                }
            }

            $detail->invoice->updateStatusIfPaid();
        });

        static::updating(function (InvoiceDetail $detail) {
            $item = $detail->content ?? $detail->product;

            if ($item instanceof Product && $item->inventory) {
                $oldQuantity = $detail->getOriginal('quantity');
                $newQuantity = +$detail->quantity;
                $diff = $newQuantity - $oldQuantity;
                $amountInInventory = $item->inventory->amount;

                if ($detail->invoice->invoice_type === InvoiceType::INVENTORY->value) {
                    $item->inventory->update(['amount' => $amountInInventory + $diff]);
                } else {
                    $item->inventory->update(['amount' => $amountInInventory - $diff]);
                }
            }
        });

        static::updated(function (InvoiceDetail $detail) {
            $detail->invoice->updateStatusIfPaid();
        });

        static::deleted(function (InvoiceDetail $detail) {
            $item = $detail->content ?? $detail->product;
            $quantity = $detail->quantity;

            if ($item instanceof Product && $item->inventory) {
                $amount = $item->inventory->amount;

                if ($detail->invoice->invoice_type === InvoiceType::INVENTORY->value) {
                    $item->inventory->update(['amount' => $amount - $quantity]);
                } else {
                    $item->inventory->update(['amount' => $amount + $quantity]);
                }
            }

            $detail->invoice->updateStatusIfPaid();
        });
    }
}
