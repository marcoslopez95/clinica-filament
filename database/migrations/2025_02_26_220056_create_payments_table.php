<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
            ->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('payment_method_id')
            ->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('currency_id')
            ->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->float('amount');
            $table->float('exchange');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
