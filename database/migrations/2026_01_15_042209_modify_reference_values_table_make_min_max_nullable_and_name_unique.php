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
        Schema::table('reference_values', function (Blueprint $table) {
            $table->float('min_value')->nullable()->change();
            $table->float('max_value')->nullable()->change();
            $table->unique(['exam_id', 'name', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reference_values', function (Blueprint $table) {
            $table->float('min_value')->nullable(false)->change();
            $table->float('max_value')->nullable(false)->change();
            $table->dropUnique(['reference_values_exam_id_name_deleted_at_unique']);
        });
    }
};
