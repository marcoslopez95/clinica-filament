<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            // Make quantity nullable. Requires doctrine/dbal to change column type/nullability.
            $table->float('quantity')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            // Revert to non-nullable
            $table->float('quantity')->nullable(false)->change();
        });
    }
};
