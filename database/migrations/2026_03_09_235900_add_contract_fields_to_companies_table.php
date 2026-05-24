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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'training_end_date')) {
                $table->date('training_end_date')->nullable()->after('contract_start_date');
            }

            if (!Schema::hasColumn('companies', 'contract_amount')) {
                $table->unsignedBigInteger('contract_amount')->nullable()->after('training_end_date');
            }

            if (!Schema::hasColumn('companies', 'payment_status')) {
                $table->string('payment_status')->default('not_billed')->after('contract_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('companies', 'payment_status')) {
                $columns[] = 'payment_status';
            }

            if (Schema::hasColumn('companies', 'contract_amount')) {
                $columns[] = 'contract_amount';
            }

            if (Schema::hasColumn('companies', 'training_end_date')) {
                $columns[] = 'training_end_date';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
