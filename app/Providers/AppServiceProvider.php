<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Observers\Invoice\InvoiceObserver;
use App\Observers\Invoice\PaymentObserver;
use App\Observers\Invoice\InvoiceDiscountObserver;
use App\Observers\InvoiceDetail\InvoiceDetailInventoryObserver;
use App\Observers\InvoiceDetail\InvoiceDetailStatusObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        Invoice::observe(InvoiceObserver::class);
        \App\Models\Payment::observe(PaymentObserver::class);
        \App\Models\InvoiceDiscount::observe(InvoiceDiscountObserver::class);
        InvoiceDetail::observe(InvoiceDetailInventoryObserver::class);
        InvoiceDetail::observe(InvoiceDetailStatusObserver::class);
    }
}
