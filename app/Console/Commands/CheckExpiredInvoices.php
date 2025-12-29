<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class CheckExpiredInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como vencidas las facturas cuya fecha de crédito ha expirado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');

        $affected = Invoice::whereNotNull('credit_date')
            ->where('credit_date', '<', $today)
            ->where('is_expired', false)
            ->update(['is_expired' => true]);

        $this->info("Se han marcado {$affected} facturas como vencidas.");
    }
}
