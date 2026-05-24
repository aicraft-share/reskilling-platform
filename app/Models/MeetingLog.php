<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\User;

class MeetingLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'started_at',
        'youtube_url',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_start_url',
        'zoom_status',
        'zoom_uuid',
        'host_email',
        'end_time',
        'duration_minutes',
        'zoom_sync_status',
        'zoom_synced_at',
        'zoom_sync_error',
        'memo',
        'created_by',
        'transcript_text',
        'transcript_status',
        'transcript_source',
        'transcript_source',
        'transcript_uploaded_at',
        'transcript_summary',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'end_time' => 'datetime',
        'zoom_synced_at' => 'datetime',
        'transcript_uploaded_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'meeting_log_students', 'meeting_log_id', 'student_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(MeetingLogParticipant::class);
    }

    public function meeting()
    {
        return $this->hasOne(\App\Models\Meeting::class, 'meeting_log_id');
    }

    public function getScheduledAtAttribute()
    {
        return $this->started_at;
    }
}
