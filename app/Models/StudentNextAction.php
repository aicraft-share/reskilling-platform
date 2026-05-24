<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentNextAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'instruction_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The student this action is for.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The teacher who set this action.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The lecture pages associated with this action.
     */
    public function lecturePages(): BelongsToMany
    {
        return $this->belongsToMany(
            LecturePage::class,
            'student_next_action_lessons',
            'student_next_action_id',
            'lecture_page_id'
        )->withTimestamps();
    }
}
