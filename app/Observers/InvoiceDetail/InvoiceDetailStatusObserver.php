<?php

namespace App\Observers\InvoiceDetail;

use App\Models\InvoiceDetail;
use App\Services\InvoiceStatusService;

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
        $service = new InvoiceStatusService();
        $service->updateStatus($detail->invoice);
    }
}
