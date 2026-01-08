<?php

namespace App\Observers;

use App\Enums\InvoiceType;
use App\Models\InvoiceDetail;
use App\Models\Product;

class InvoiceDetailObserver
{
    /**
     * Handle the InvoiceDetail "created" event.
     */
    public function created(InvoiceDetail $invoiceDetail): void
    {
        $this->adjustInventoryOnCreate($invoiceDetail);
    }

    /**
     * Handle the InvoiceDetail "updated" event.
     */
    public function updated(InvoiceDetail $invoiceDetail): void
    {
        $this->adjustInventoryOnUpdate($invoiceDetail);
    }

    /**
     * Handle the InvoiceDetail "deleted" event.
     */
    public function deleted(InvoiceDetail $invoiceDetail): void
    {
        $this->adjustInventoryOnDelete($invoiceDetail);
    }

    /**
     * Handle the InvoiceDetail "restored" event.
     */
    public function restored(InvoiceDetail $invoiceDetail): void
    {
        // Not handled by this observer per requirements.
    }

    /**
     * Handle the InvoiceDetail "force deleted" event.
     */
    public function forceDeleted(InvoiceDetail $invoiceDetail): void
    {
        // Not handled by this observer per requirements.
    }

    protected function adjustInventoryOnCreate(InvoiceDetail $detail): void
    {
        $item = $detail->content ?? $detail->product;

        if (!($item instanceof Product) || !$item->inventory) {
            return;
        }

        $quantity = (int) $detail->quantity;

        if ($detail->invoice->invoice_type === InvoiceType::INVENTORY) {
            $item->inventory->update(['amount' => $item->inventory->amount + $quantity]);
        } else {
            $item->inventory->update(['amount' => $item->inventory->amount - $quantity]);
        }
    }

    protected function adjustInventoryOnUpdate(InvoiceDetail $detail): void
    {
        $item = $detail->content ?? $detail->product;

        if (!($item instanceof Product) || !$item->inventory) {
            return;
        }

        $oldQuantity = (int) $detail->getOriginal('quantity');
        $newQuantity = (int) $detail->quantity;
        $diff = $newQuantity - $oldQuantity;

        if ($diff === 0) {
            return;
        }

        if ($detail->invoice->invoice_type === InvoiceType::INVENTORY) {
            $item->inventory->update(['amount' => $item->inventory->amount + $diff]);
        } else {
            $item->inventory->update(['amount' => $item->inventory->amount - $diff]);
        }
    }

    protected function adjustInventoryOnDelete(InvoiceDetail $detail): void
    {
        $item = $detail->content ?? $detail->product;

        if (!($item instanceof Product) || !$item->inventory) {
            return;
        }

        $quantity = (int) $detail->quantity;

        if ($detail->invoice->invoice_type === InvoiceType::INVENTORY) {
            $item->inventory->update(['amount' => $item->inventory->amount - $quantity]);
        } else {
            $item->inventory->update(['amount' => $item->inventory->amount + $quantity]);
        }
    }
}
