<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_value_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_detail_id')->constrained('invoice_details')->onDelete('cascade');
            $table->foreignId('reference_value_id')->constrained('reference_values')->onDelete('cascade');
            $table->string('result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_value_results');
    }
};
