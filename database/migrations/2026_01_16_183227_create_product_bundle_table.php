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
        Schema::create('product_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 12, 4)->default(1);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product');
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
        });
    }
};
