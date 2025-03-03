<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected static function booted(): void
    {
        static::created(function (InvoiceDetail $detail) {
            $amount = $detail->product->inventory->amount;
            $detail->product->inventory->update(['amount' => $amount - $detail->quantity]);
        });

        static::updating(function (InvoiceDetail $detail) {
            $oldQuantity = $detail->getOriginal('quantity');
            $newQuantity = +$detail->quantity;
            $totalToDiscount = $newQuantity - $oldQuantity;
            $amountInInventory = $detail->product->inventory->amount;
            $detail->product->inventory->update(['amount' => $amountInInventory - $totalToDiscount]);
        });
    }
}
