<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            if (Schema::hasColumn('refunds', 'payment_id')) {
                $table->dropColumn('payment_id');
            }

            if (! Schema::hasColumn('refunds', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->foreign('invoice_id')->references('id')->on('invoices');
            }
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            if (! Schema::hasColumn('refunds', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable();
            }

            if (Schema::hasColumn('refunds', 'invoice_id')) {
                $table->dropForeign(['invoice_id']);
                $table->dropColumn('invoice_id');
            }
        });
    }
};
