<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Sync existing teacher_id data from companies table to company_user pivot table.
     */
    public function up(): void
    {
        // Get all companies that have a teacher_id set
        $companies = DB::table('companies')
            ->whereNotNull('teacher_id')
            ->get(['id', 'teacher_id']);

        foreach ($companies as $company) {
            // Only insert if the relationship doesn't already exist
            $exists = DB::table('company_user')
                ->where('company_id', $company->id)
                ->where('user_id', $company->teacher_id)
                ->exists();

            if (!$exists) {
                DB::table('company_user')->insert([
                    'company_id' => $company->id,
                    'user_id' => $company->teacher_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't remove data on rollback to avoid data loss.
        // The company_user entries created by this migration are safe to keep.
    }
};
