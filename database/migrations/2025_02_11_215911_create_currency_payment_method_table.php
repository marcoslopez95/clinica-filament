<?php

use App\Models\Currency;
use App\Models\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('currency_payment_method', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Currency::class)
            ->constrained()
            ->cascadeOnUpdate()
            ->restrictOnDelete();
            $table->foreignIdFor(PaymentMethod::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_payment_method');
    }
};
