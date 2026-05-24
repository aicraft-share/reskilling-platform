<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'thumbnail_path',
        'status',
        'sort_order',
    ];

    public function lecturePages(): HasMany
    {
        return $this->hasMany(LecturePage::class, 'course_id')->orderBy('sort_order', 'asc');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
