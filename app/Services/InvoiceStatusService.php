<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;

class InvoiceStatusService
{
    public function updateStatus(Invoice $invoice): void
    {
        $invoice->refresh();

        if ($invoice->status === InvoiceStatus::CANCELLED) {
            return;
        }

        if ($invoice->invoice_type === InvoiceType::INVENTORY) {
            $this->updateInventoryStatus($invoice);
        } else {
            $this->updateInvoiceStatus($invoice);
        }
    }

    protected function updateInventoryStatus(Invoice $invoice): void
    {
        $total = (float) $invoice->total;
        $totalPaid = (float) $invoice->total_paid;
        $totalDiscounts = (float) $invoice->discounts->sum('amount');
        $hasMoney = $totalPaid > 0 || $totalDiscounts > 0;

        if ($invoice->isComplete()) {
            $invoice->update(['status' => InvoiceStatus::CLOSED]);
            return;
        }

        if ($hasMoney) {
            $invoice->update(['status' => InvoiceStatus::PARTIAL]);
            return;
        }

        $invoice->update(['status' => InvoiceStatus::OPEN]);
    }

    protected function updateInvoiceStatus(Invoice $invoice): void
    {
        $total = (float) $invoice->total;
        $totalPaid = (float) $invoice->total_paid;
        $totalDiscounts = (float) $invoice->discounts->sum('amount');
        $sumPaymentsAndDiscounts = $totalPaid + $totalDiscounts;

        if ($total > 0 && $sumPaymentsAndDiscounts >= ($total - 0.01)) {
            $invoice->update(['status' => InvoiceStatus::CLOSED]);
            return;
        }

        if ($total > 0 && $sumPaymentsAndDiscounts > 0 && $sumPaymentsAndDiscounts < ($total - 0.01)) {
            $invoice->update(['status' => InvoiceStatus::PARTIAL]);
            return;
        }

        if ($total <= 0 && $sumPaymentsAndDiscounts > 0) {
            $invoice->update(['status' => InvoiceStatus::PARTIAL]);
            return;
        }

        $invoice->update(['status' => InvoiceStatus::OPEN]);
    }
}
