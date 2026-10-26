<?php

namespace App\Models;

use App\Enums\ExamType;
use App\Traits\HasJalaliDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasJalaliDates;

    protected $fillable = [
        'standard_id',
        'product_id',
        'title',
        'type',
        'description',
        'question_count',
        'selection_mode',
        'seconds_per_question',
        'pass_percentage',
        'max_attempts',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExamType::class,
            'question_count' => 'integer',
            'seconds_per_question' => 'integer',
            'questions_snapshot' => 'array',
            'pass_percentage' => 'integer',
            'max_attempts' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function durationInSeconds(): int
    {
        return $this->question_count * $this->seconds_per_question;
    }

    public function blueprintRules(): HasMany
    {
        return $this->hasMany(ExamBlueprintRule::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function chapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
