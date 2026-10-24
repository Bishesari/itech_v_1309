<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttemptQuestion extends Model
{
    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'position',
        'question_snapshot',
        'options_snapshot',
        'selected_option_key',
        'is_correct',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'question_snapshot' => 'array',
            'options_snapshot' => 'array',
            'is_correct' => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
