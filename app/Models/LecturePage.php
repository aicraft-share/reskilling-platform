<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LecturePage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'section_name',
        'title',
        'description',
        'sort_order',
        'is_active',
        'thumbnail_path',
        'youtube_url',
        'youtube_video_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'course_id' => 'integer',
    ];

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function lectureVideoProgresses(): HasMany
    {
        return $this->hasMany(LectureVideoProgress::class);
    }

    public function studentNextActions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(StudentNextAction::class, 'student_next_action_lessons', 'lecture_page_id', 'student_next_action_id');
    }
}
