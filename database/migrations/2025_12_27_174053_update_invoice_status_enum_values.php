<?php

use App\Enums\InvoiceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('invoices')
            ->where('status', 'Abierta')
            ->update(['status' => InvoiceStatus::OPEN->value]);

        DB::table('invoices')
            ->where('status', 'Cerrado')
            ->update(['status' => InvoiceStatus::CLOSED->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('invoices')
            ->where('status', InvoiceStatus::OPEN->value)
            ->update(['status' => 'Abierta']);

        DB::table('invoices')
            ->where('status', InvoiceStatus::CLOSED->value)
            ->update(['status' => 'Cerrado']);
    }
};
