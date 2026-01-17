<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('invoices')
            ->where('invoice_type', 3) // InvoiceType::COTIZACION->value
            ->update([
                'is_quotation' => true,
                'invoice_type' => 3 // InvoiceType::DEFAULT->value (Quirofano)
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('invoices')
            ->where('is_quotation', true)
            ->update([
                'invoice_type' => 3 // InvoiceType::COTIZACION->value
            ]);
    }
};
