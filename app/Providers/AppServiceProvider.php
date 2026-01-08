<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Models\InvoiceDetail;
use App\Observers\InvoiceDetailObserver;
use App\Observers\InvoiceDetailStatusObserver;

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

        InvoiceDetail::observe(InvoiceDetailObserver::class);
        InvoiceDetail::observe(InvoiceDetailStatusObserver::class);
    }
}
