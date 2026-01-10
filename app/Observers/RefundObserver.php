<?php

namespace App\Observers;

use App\Models\Refund;
use App\Services\InvoiceStatusService;

class RefundObserver
{
    public function created(Refund $refund): void
    {
        if ($refund->invoice) {
            (new InvoiceStatusService())->updateStatus($refund->invoice);
        }
    }

    public function updated(Refund $refund): void
    {
        if ($refund->invoice) {
            (new InvoiceStatusService())->updateStatus($refund->invoice);
        }
    }

    public function deleted(Refund $refund): void
    {
        if ($refund->invoice) {
            (new InvoiceStatusService())->updateStatus($refund->invoice);
        }
    }

    public function restored(Refund $refund): void
    {
        if ($refund->invoice) {
            (new InvoiceStatusService())->updateStatus($refund->invoice);
        }
    }
}
