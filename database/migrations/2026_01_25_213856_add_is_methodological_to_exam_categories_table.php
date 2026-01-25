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
        Schema::table('exam_categories', function (Blueprint $table) {
            $table->boolean('is_methodological')->default(false)->after('show_exam_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_categories', function (Blueprint $table) {
            $table->dropColumn('is_methodological');
        });
    }
};
