<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'status',
        'contract_start_date',
        'training_end_date',
        'contract_amount',
        'payment_status',
        'business_description',
        'teacher_id',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'training_end_date' => 'date',
        'contract_amount' => 'integer',
    ];

    // Status Constants
    const STATUS_FREE_TRIAL = 'free_trial';
    const STATUS_ACTIVE = 'active';
    const STATUS_FINISHED = 'finished';

    // Payment Status Constants
    const PAYMENT_NOT_BILLED = 'not_billed';
    const PAYMENT_BILLED = 'billed';
    const PAYMENT_WAITING_PAYMENT = 'waiting_payment';
    const PAYMENT_PAID = 'paid';

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_FREE_TRIAL => '無料研修中',
            self::STATUS_ACTIVE => '研修中',
            self::STATUS_FINISHED => '修了済',
            default => '不明',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_FREE_TRIAL => 'bg-amber-100 text-amber-800',
            self::STATUS_ACTIVE => 'bg-emerald-100 text-emerald-800',
            self::STATUS_FINISHED => 'bg-slate-100 text-slate-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_NOT_BILLED => '未請求',
            self::PAYMENT_BILLED => '請求済み',
            self::PAYMENT_WAITING_PAYMENT => '支払い待ち',
            self::PAYMENT_PAID => '支払済',
            default => '-',
        };
    }

    public function getPaymentStatusClassAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_NOT_BILLED => 'bg-slate-100 text-slate-700',
            self::PAYMENT_BILLED => 'bg-blue-100 text-blue-800',
            self::PAYMENT_WAITING_PAYMENT => 'bg-orange-100 text-orange-800',
            self::PAYMENT_PAID => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-500',
        };
    }

    // Legacy: Single teacher assigned to this company (kept for backward compatibility)
    public function teacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Teachers assigned to this company (Many-to-Many - primary relationship)
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user');
    }

    /**
     * Get comma-separated teacher names for display.
     */
    public function getTeacherNamesAttribute(): string
    {
        $names = $this->teachers->pluck('name')->map(function ($name) {
            return preg_replace('/\s*[（(].*[）)]\s*$/u', '', $name);
        });

        if ($names->isEmpty() && $this->teacher) {
            $legacyName = preg_replace('/\s*[（(].*[）)]\s*$/u', '', $this->teacher->name);
            return $legacyName ?: '未割り当て';
        }

        return $names->isNotEmpty() ? $names->join(', ') : '未割り当て';
    }

    // Students belonging to this company
    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', User::ROLE_STUDENT);
    }
}
