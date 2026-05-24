<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add login_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_id')->nullable()->unique()->after('id');
        });

        // Add email to companies
        Schema::table('companies', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
        });

        // Generate IDs for existing users
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $prefix = match ($user->role) {
                'admin' => 'adm_',
                'teacher' => 'ins_',
                'student' => 'std_',
                'company' => 'cpn_',
                default => 'usr_',
            };
            
            $loginId = $prefix . strtolower(Str::random(5));
            
            // Ensure uniqueness
            while (DB::table('users')->where('login_id', $loginId)->exists()) {
                $loginId = $prefix . strtolower(Str::random(5));
            }

            DB::table('users')->where('id', $user->id)->update(['login_id' => $loginId]);
        }

        // Change login_id to non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
