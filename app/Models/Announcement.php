<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'target_scope_type',
        'created_by_admin_id',
    ];

    /**
     * Scope mapping for the delivery targets.
     */
    const SCOPE_ALL = 'all';
    const SCOPE_INSTRUCTORS = 'instructors';
    const SCOPE_COMPANIES = 'companies';
    const SCOPE_STUDENTS = 'students';

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function recipients()
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }
}
