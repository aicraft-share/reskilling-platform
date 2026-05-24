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
        Schema::table('lecture_pages', function (Blueprint $header) {
            $header->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
            $header->string('section_name')->nullable()->after('course_id');
            // 'sort_order' already exists, we'll use it as 'order_in_course'.
            // The user suggested renaming it, but let's keep it 'sort_order' for now 
            // since the existing code uses it. I'll stick to 'sort_order' to avoid refactoring everywhere.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lecture_pages', function (Blueprint $header) {
            $header->dropForeign(['course_id']);
            $header->dropColumn(['course_id', 'section_name']);
        });
    }
};
