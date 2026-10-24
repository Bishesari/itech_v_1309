<?php

namespace App\Models;

use App\Enums\ExamAttemptStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'order_item_id',
        'status',
        'started_at',
        'expires_at',
        'submitted_at',
        'question_count',
        'duration_seconds',
        'correct_answers',
        'score_percentage',
        'passed',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
            'question_count' => 'integer',
            'duration_seconds' => 'integer',
            'correct_answers' => 'integer',
            'score_percentage' => 'decimal:2',
            'passed' => 'boolean',
            'status' => ExamAttemptStatus::class,
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamAttemptQuestion::class)
            ->orderBy('position');
    }
}
