<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Refund;

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
        $paymentsTotal = $this->sumPayments($invoice);
        $refundsTotal = $this->sumRefunds($invoice);
        $totalPaid = $paymentsTotal - $refundsTotal;
        $totalDiscounts = (float) $invoice->discounts->sum('amount');

        if ($invoice->isComplete()) {
            $invoice->update(['status' => InvoiceStatus::CLOSED]);
            return;
        }

        // If payments and refunds cancel each other out
        if (round($paymentsTotal, 2) === round($refundsTotal, 2)) {
            if ($totalDiscounts > 0) {
                $invoice->update(['status' => InvoiceStatus::PARTIAL]);
                return;
            }

            $invoice->update(['status' => InvoiceStatus::OPEN]);
            return;
        }

        // If there's a difference between payments and refunds, consider partial (unless complete)
        $invoice->update(['status' => InvoiceStatus::PARTIAL]);
    }

    protected function updateInvoiceStatus(Invoice $invoice): void
    {
        $total = (float) $invoice->total;
        $paymentsTotal = $this->sumPayments($invoice);
        $refundsTotal = $this->sumRefunds($invoice);
        $totalPaid = $paymentsTotal - $refundsTotal;
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

        // Special case: if raw payments equal raw refunds
        if (round($paymentsTotal, 2) === round($refundsTotal, 2)) {
            if ($totalDiscounts > 0) {
                $invoice->update(['status' => InvoiceStatus::PARTIAL]);
                return;
            }

            $invoice->update(['status' => InvoiceStatus::OPEN]);
            return;
        }

        $invoice->update(['status' => InvoiceStatus::OPEN]);
    }

    protected function sumPayments(Invoice $invoice): float
    {
        return (float) $invoice->payments->sum(function ($item) {
            $exchange   = (float) ($item->exchange ?? 1);
            $amount     = (float) ($item->amount ?? 0);
            $currencyId = (int) ($item->currency_id ?? 0);

            if ($exchange <= 0) $exchange = 1;

            return $currencyId === 1 ? $amount : $amount / $exchange;
        });
    }

    protected function sumRefunds(Invoice $invoice): float
    {
        return (float) $invoice->refunds()->get()->sum(function ($item) {
            $exchange   = (float) ($item->exchange ?? 1);
            $amount     = (float) ($item->amount ?? 0);
            $currencyId = (int) ($item->currency_id ?? 0);

            if ($exchange <= 0) $exchange = 1;

            return $currencyId === 1 ? $amount : $amount / $exchange;
        });
    }
}
