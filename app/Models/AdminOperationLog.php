<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminOperationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_admin_id',
        'actor_admin_name',
        'action_type',
        'target_type',
        'target_id',
        'target_label',
        'changed_fields',
        'before_values',
        'after_values',
        'route_name',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'before_values' => 'array',
        'after_values' => 'array',
        'created_at' => 'datetime',
    ];

    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_STATUS_CHANGE = 'status_change';

    public const ACTION_LABELS = [
        self::ACTION_CREATE => '作成',
        self::ACTION_UPDATE => '更新',
        self::ACTION_DELETE => '削除',
        self::ACTION_STATUS_CHANGE => 'ステータス変更',
    ];

    public const TARGET_LABELS = [
        'company' => '企業',
        'student' => '生徒',
        'instructor' => '講師',
        'assignment' => '課題',
        'curriculum' => 'カリキュラム',
        'admin_user' => '管理者',
        'announcement' => 'お知らせ',
        'setting' => '設定',
    ];

    public const FIELD_LABELS = [
        'name' => '名前',
        'email' => 'メールアドレス',
        'status' => 'ステータス',
        'title' => 'タイトル',
        'description' => '説明',
        'body' => '本文',
        'target_scope_type' => '配信先',
        'company_id' => '企業',
        'company_ids' => '担当企業',
        'teacher_id' => '担当講師',
        'contract_start_date' => '研修開始日',
        'training_end_date' => '研修終了日',
        'contract_amount' => '契約金額',
        'payment_status' => '支払い状況',
        'sort_order' => '表示順',
        'is_active' => '公開状態',
        'thumbnail_path' => 'サムネイル',
        'youtube_url' => 'YouTube URL',
        'youtube_video_id' => 'YouTube動画ID',
        'years_of_experience' => '経験年数',
        'specialty_fields' => '専門分野',
        'skills' => 'スキル',
        'recipient_count' => '配信件数',
        'password' => 'パスワード',
        'notify_assignment_submitted' => '課題提出通知',
        'notify_new_chat' => 'チャット通知',
        'notify_mtg_updated' => 'MTG更新通知',
        'notify_feedback_posted' => 'FB投稿通知',
        'notify_learning_updated' => '学習更新通知',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_admin_id');
    }

    public function getActionTypeLabelAttribute(): string
    {
        return self::ACTION_LABELS[$this->action_type] ?? $this->action_type;
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return self::TARGET_LABELS[$this->target_type] ?? $this->target_type;
    }

    public static function fieldLabel(string $field): string
    {
        return self::FIELD_LABELS[$field] ?? $field;
    }
}
