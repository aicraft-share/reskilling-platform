<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LectureVideoProgress extends Model
{
    protected $table = 'lecture_video_progresses';

    protected $guarded = [];

    protected $casts = [
        'last_watched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lecturePage()
    {
        return $this->belongsTo(LecturePage::class);
    }
}
