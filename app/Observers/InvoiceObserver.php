<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Inventory;
use App\Models\Product;

class InvoiceObserver
{
    public function creating(Invoice $invoice): void
    {
        if (!$invoice->status) {
            $invoice->status = InvoiceStatus::OPEN;
        }
    }

    public function updated(Invoice $invoice): void
    {
        $original = $invoice->getOriginal('status');
        $current = $invoice->status instanceof InvoiceStatus ? $invoice->status->value : $invoice->status;

        if ($original === $current) {
            return;
        }

        if ((string) $current !== (string) InvoiceStatus::CANCELLED->value) {
            return;
        }

        foreach ($invoice->details()->where('content_type', Product::class)->get() as $detail) {
            $productId = $detail->content_id ?? $detail->product_id;
            if (!$productId) {
                continue;
            }

            $inventory = Inventory::where('product_id', $productId)->first();
            if (!$inventory) {
                continue;
            }

            $quantity = (int) ($detail->quantity ?? 0);

            if ($invoice->invoice_type === InvoiceType::INVENTORY) {
                $inventory->decrement('amount', $quantity);
            } else {
                $inventory->increment('amount', $quantity);
            }
        }
    }
}
