<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class InvoiceObserver
{
    public function creating(Invoice $invoice): void
    {
        if (!$invoice->status) {
            $invoice->status = InvoiceStatus::OPEN;
        }
    }
}
