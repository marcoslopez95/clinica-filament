<?php

namespace App\Observers\Invoice;

use App\Models\Payment;
use App\Services\InvoiceStatusService;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        (new InvoiceStatusService())->updateStatus($payment->invoice);
    }

    public function updated(Payment $payment): void
    {
        (new InvoiceStatusService())->updateStatus($payment->invoice);
    }

    public function deleted(Payment $payment): void
    {
        (new InvoiceStatusService())->updateStatus($payment->invoice);
    }
}
