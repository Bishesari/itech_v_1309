<?php

namespace App\Models;

use App\Enums\QuestionDifficulty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamBlueprintRule extends Model
{
    protected $fillable = [
        'exam_id',
        'chapter_id',
        'difficulty',
        'question_count',
    ];

    protected function casts(): array
    {
        return [
            'difficulty' => QuestionDifficulty::class,
            'question_count' => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
