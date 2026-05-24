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
        Schema::create('student_next_actions', function (Blueprint $row) {
            $row->id();
            $row->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $row->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $row->text('instruction_text');
            $row->boolean('is_active')->default(true);
            $row->timestamps();
        });

        Schema::create('student_next_action_lessons', function (Blueprint $row) {
            $row->id();
            $row->foreignId('student_next_action_id')->constrained('student_next_actions')->onDelete('cascade');
            $row->foreignId('lecture_page_id')->constrained('lecture_pages')->onDelete('cascade');
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_next_action_lessons');
        Schema::dropIfExists('student_next_actions');
    }
};
