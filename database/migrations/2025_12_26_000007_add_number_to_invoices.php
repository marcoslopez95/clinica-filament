<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->unique()->after('id');
            $table->string('invoice_type')->after('invoice_number');
            $table->unsignedBigInteger('type_document_id')->after('invoice_type');
            $table->date('credit_date')->nullable()->after('type_document_id');
            $table->foreign('type_document_id')->references('id')->on('type_documents');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['type_document_id']);
            $table->dropUnique(['invoice_number']);
            $table->dropColumn(['invoice_number', 'invoice_type', 'type_document_id', 'credit_date']);
        });
    }
};
