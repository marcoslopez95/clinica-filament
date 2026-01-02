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
        Schema::table('refunds', function (Blueprint $table) {
            // Eliminar el campo invoice_id
            $table->dropColumn('invoice_id');

            // Crear el campo payment_id en su lugar
            $table->unsignedBigInteger('payment_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Eliminar payment_id
            $table->dropColumn('payment_id');

            // Restaurar invoice_id
            $table->unsignedBigInteger('invoice_id')->nullable();
        });
    }
};
