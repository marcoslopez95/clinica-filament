<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
use App\Enums\InvoiceType;
use App\Enums\InvoiceStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('invoices')->where('invoice_type', 'Default')->update(['invoice_type' => InvoiceType::DEFAULT->value]);
        DB::table('invoices')->where('invoice_type', 'Inventory')->update(['invoice_type' => InvoiceType::INVENTORY->value]);
        DB::table('invoices')->where('invoice_type', 'Laboratory')->update(['invoice_type' => InvoiceType::LABORATORY->value]);

        DB::table('invoices')->where('status', 'Por pagar')->update(['status' => InvoiceStatus::OPEN->value]);
        DB::table('invoices')->where('status', 'Pagada')->update(['status' => InvoiceStatus::CLOSED->value]);
        DB::table('invoices')->where('status', 'Cancelado')->update(['status' => InvoiceStatus::CANCELLED->value]);
        DB::table('invoices')->where('status', 'Pago parcial')->update(['status' => InvoiceStatus::PARTIAL->value]);
        DB::table('invoices')->where('status', 'Vencida')->update(['status' => InvoiceStatus::OPEN->value]);

        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('invoice_type')->default(InvoiceType::DEFAULT->value)->change();
            $table->integer('status')->default(InvoiceStatus::OPEN->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revertir el tipo de columna a string
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_type')->change();
            $table->string('status')->change();
        });

        // 2. Revertir los números a texto
        DB::table('invoices')->where('invoice_type', InvoiceType::DEFAULT->value)->update(['invoice_type' => 'Default']);
        DB::table('invoices')->where('invoice_type', InvoiceType::INVENTORY->value)->update(['invoice_type' => 'Inventory']);
        DB::table('invoices')->where('invoice_type', InvoiceType::LABORATORY->value)->update(['invoice_type' => 'Laboratory']);

        DB::table('invoices')->where('status', InvoiceStatus::OPEN->value)->update(['status' => 'Por pagar']);
        DB::table('invoices')->where('status', InvoiceStatus::CLOSED->value)->update(['status' => 'Pagada']);
        DB::table('invoices')->where('status', InvoiceStatus::CANCELLED->value)->update(['status' => 'Cancelado']);
        DB::table('invoices')->where('status', InvoiceStatus::PARTIAL->value)->update(['status' => 'Pago parcial']);
    }
};
