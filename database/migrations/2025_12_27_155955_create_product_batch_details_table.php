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
        Schema::create('product_batch_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_detail_id')->constrained()->cascadeOnDelete();
            $table->date('expiration_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batch_details');
    }
};
