<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->unsignedBigInteger('content_id')->nullable();
            $table->string('content_type')->nullable();
        });

        DB::table('invoice_details')->update([
            'content_id'   => DB::raw('product_id'),
            'content_type' => 'App\\Models\\Product',
        ]);

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
        });

        DB::table('invoice_details')
            ->where('content_type', 'App\\Models\\Product')
            ->update([
                'product_id' => DB::raw('content_id'),
            ]);

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropColumn(['content_id', 'content_type']);
        });
    }
};

