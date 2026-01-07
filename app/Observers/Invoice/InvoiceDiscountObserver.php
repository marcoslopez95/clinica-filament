<?php

namespace App\Observers\Invoice;

use App\Models\InvoiceDiscount;
use App\Services\InvoiceStatusService;

class InvoiceDiscountObserver
{
    public function created(InvoiceDiscount $discount): void
    {
        (new InvoiceStatusService())->updateStatus($discount->invoice);
    }

    public function updated(InvoiceDiscount $discount): void
    {
        (new InvoiceStatusService())->updateStatus($discount->invoice);
    }

    public function deleted(InvoiceDiscount $discount): void
    {
        (new InvoiceStatusService())->updateStatus($discount->invoice);
    }
}
