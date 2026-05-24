<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STUDENT = 'student';
    const ROLE_COMPANY = 'company';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'login_id',
        'password',
        'avatar_path',
        'role',
        'company_id',
        'notify_assignment_submitted',
        'notify_new_chat',
        'notify_mtg_updated',
        'notify_feedback_posted',
        'notify_learning_updated',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_assignment_submitted' => 'boolean',
            'notify_new_chat' => 'boolean',
            'notify_mtg_updated' => 'boolean',
            'notify_feedback_posted' => 'boolean',
            'notify_learning_updated' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isInstructor(): bool
    {
        return $this->isTeacher();
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isCompany(): bool
    {
        return $this->role === self::ROLE_COMPANY;
    }

    // Student belongs to a company
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Teacher works for many companies (Many-to-Many via company_user pivot)
    public function assignedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user');
    }

    // Student has many submissions
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    // Teacher has one profile
    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    // Student: Meetings this user participates in
    public function participatingMeetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants', 'student_id', 'meeting_id')
            ->withTimestamps();
    }

    // Student: YouTube video watching progress
    public function lectureVideoProgresses(): HasMany
    {
        return $this->hasMany(LectureVideoProgress::class);
    }

    // Student: Chat threads
    public function chatThreadsAsStudent(): HasMany
    {
        return $this->hasMany(ChatThread::class, 'student_id');
    }

    // Instructor: Chat threads
    public function chatThreadsAsInstructor(): HasMany
    {
        return $this->hasMany(ChatThread::class, 'instructor_id');
    }

    /**
     * Next actions set for this student.
     */
    public function nextActions(): HasMany
    {
        return $this->hasMany(StudentNextAction::class, 'student_id');
    }

    /**
     * The current active next action for this student.
     */
    public function currentNextAction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentNextAction::class, 'student_id')->where('is_active', true)->latestOfMany();
    }
}
