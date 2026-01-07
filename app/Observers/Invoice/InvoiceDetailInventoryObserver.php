<?php

namespace App\Observers\Invoice;

use App\Enums\InvoiceType;
use App\Models\InvoiceDetail;
use App\Models\Inventory;
use App\Models\Product;

class InvoiceDetailInventoryObserver
{
    public function created(InvoiceDetail $invoiceDetail): void
    {
        $this->adjustInventoryOnCreate($invoiceDetail);
    }

    public function updated(InvoiceDetail $invoiceDetail): void
    {
        $this->adjustInventoryOnUpdate($invoiceDetail);
    }

    public function deleted(InvoiceDetail $invoiceDetail): void
    {
        $this->adjustInventoryOnDelete($invoiceDetail);
    }

    protected function inventoryForDetail(InvoiceDetail $detail): ?Inventory
    {
        $item = $detail->content ?? $detail->product;

        if ($item instanceof Product && $item->inventory) {
            return $item->inventory;
        }

        if ($detail->product_id) {
            return Inventory::where('product_id', $detail->product_id)->first();
        }

        return null;
    }

    protected function adjustInventoryOnCreate(InvoiceDetail $detail): void
    {
        $inventory = $this->inventoryForDetail($detail);
        if (!$inventory) {
            return;
        }

        $quantity = (int) $detail->quantity;

        if ($detail->invoice->invoice_type === InvoiceType::INVENTORY) {
            $inventory->update(['amount' => $inventory->amount + $quantity]);
        } else {
            $inventory->update(['amount' => $inventory->amount - $quantity]);
        }
    }

    protected function adjustInventoryOnUpdate(InvoiceDetail $detail): void
    {
        $inventory = $this->inventoryForDetail($detail);
        if (!$inventory) {
            return;
        }

        $oldQuantity = (int) $detail->getOriginal('quantity');
        $newQuantity = (int) $detail->quantity;
        $diff = $newQuantity - $oldQuantity;

        if ($diff === 0) {
            return;
        }

        if ($detail->invoice->invoice_type === InvoiceType::INVENTORY) {
            $inventory->update(['amount' => $inventory->amount + $diff]);
        } else {
            $inventory->update(['amount' => $inventory->amount - $diff]);
        }
    }

    protected function adjustInventoryOnDelete(InvoiceDetail $detail): void
    {
        $inventory = $this->inventoryForDetail($detail);
        if (!$inventory) {
            return;
        }

        $quantity = (int) $detail->quantity;

        if ($detail->invoice->invoice_type === InvoiceType::INVENTORY) {
            $inventory->update(['amount' => $inventory->amount - $quantity]);
        } else {
            $inventory->update(['amount' => $inventory->amount + $quantity]);
        }
    }
}
