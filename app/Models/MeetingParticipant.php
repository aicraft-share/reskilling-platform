<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'student_id',
    ];

    protected $casts = [
        'join_time' => 'datetime',
        'leave_time' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function meetingLog()
    {
        return $this->belongsTo(MeetingLog::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
