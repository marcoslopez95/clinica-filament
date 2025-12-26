<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // If there is a foreign key on patient_id, drop it first
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'patient_id')) {
                // Attempt to drop foreign key by convention
                try {
                    $table->dropForeign(['patient_id']);
                } catch (\Throwable $e) {
                    // ignore if no foreign key
                }
            }
        });

        // Add polymorphic columns nullable first
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'invoiceable_type')) {
                $table->string('invoiceable_type')->nullable()->after('patient_id');
            }
            if (!Schema::hasColumn('invoices', 'invoiceable_id')) {
                $table->unsignedBigInteger('invoiceable_id')->nullable()->after('invoiceable_type');
            }
        });

        // Migrate existing patient_id values into polymorphic columns
        if (Schema::hasColumn('invoices', 'patient_id')) {
            DB::table('invoices')->whereNotNull('patient_id')->update([
                'invoiceable_type' => App\Models\Patient::class,
                'invoiceable_id' => DB::raw('patient_id'),
            ]);
        }

        // Make polymorphic columns non-nullable if desired and drop patient_id
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'invoiceable_type')) {
                $table->string('invoiceable_type')->nullable(false)->change();
            }
            if (Schema::hasColumn('invoices', 'invoiceable_id')) {
                $table->unsignedBigInteger('invoiceable_id')->nullable(false)->change();
            }

            if (Schema::hasColumn('invoices', 'patient_id')) {
                $table->dropColumn('patient_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate patient_id column
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'patient_id')) {
                $table->unsignedBigInteger('patient_id')->nullable()->after('invoiceable_id');
            }
        });

        // Migrate back patient_id when invoiceable_type is Patient
        if (Schema::hasColumn('invoices', 'invoiceable_type') && Schema::hasColumn('invoices', 'invoiceable_id')) {
            DB::table('invoices')
                ->where('invoiceable_type', App\Models\Patient::class)
                ->update(['patient_id' => DB::raw('invoiceable_id')]);
        }

        // Drop polymorphic columns
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'invoiceable_type')) {
                $table->dropColumn('invoiceable_type');
            }
            if (Schema::hasColumn('invoices', 'invoiceable_id')) {
                $table->dropColumn('invoiceable_id');
            }
        });
    }
};
