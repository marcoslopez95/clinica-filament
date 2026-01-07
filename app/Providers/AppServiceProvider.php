<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Models\InvoiceDetail;
use App\Observers\Invoice\InvoiceDetailInventoryObserver;
use App\Observers\Invoice\InvoiceDetailStatusObserver;

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

        InvoiceDetail::observe(InvoiceDetailInventoryObserver::class);
        InvoiceDetail::observe(InvoiceDetailStatusObserver::class);
    }
}
