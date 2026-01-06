<?php

namespace App\Observers;

use App\Models\InvoiceDetail;

class InvoiceDetailStatusObserver
{
    public function created(InvoiceDetail $detail): void
    {
        $this->updateInvoiceStatus($detail);
    }

    public function updated(InvoiceDetail $detail): void
    {
        $this->updateInvoiceStatus($detail);
    }

    public function deleted(InvoiceDetail $detail): void
    {
        $this->updateInvoiceStatus($detail);
    }

    protected function updateInvoiceStatus(InvoiceDetail $detail): void
    {
        $detail->invoice->updateStatusIfPaid();
    }
}
