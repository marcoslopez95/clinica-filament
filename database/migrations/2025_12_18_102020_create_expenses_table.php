<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
