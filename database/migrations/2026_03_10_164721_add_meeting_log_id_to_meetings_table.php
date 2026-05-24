<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->unsignedBigInteger('meeting_log_id')->nullable()->after('created_by');
            $table->foreign('meeting_log_id')->references('id')->on('meeting_logs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['meeting_log_id']);
            $table->dropColumn('meeting_log_id');
        });
    }
};
