<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_assignment_submitted')->default(true)->after('role');
            $table->boolean('notify_new_chat')->default(true)->after('notify_assignment_submitted');
            $table->boolean('notify_mtg_updated')->default(true)->after('notify_new_chat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_assignment_submitted',
                'notify_new_chat',
                'notify_mtg_updated',
            ]);
        });
    }
};
