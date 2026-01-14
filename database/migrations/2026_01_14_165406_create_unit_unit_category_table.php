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
        Schema::create('unit_unit_category', function (Blueprint $table) {
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_category_id')->constrained()->onDelete('cascade');
            $table->primary(['unit_id', 'unit_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_unit_category');
    }
};
