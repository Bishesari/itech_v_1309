<?php

namespace App\Services\Exams;

use App\Enums\ExamAttemptStatus;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinishExamAttemptService
{
    public function finish(
        int $attemptId,
        bool $expired = false,
    ): ExamAttempt {
        return DB::transaction(function () use ($attemptId, $expired) {
            $attempt = ExamAttempt::query()
                ->with(['exam', 'questions'])
                ->lockForUpdate()
                ->findOrFail($attemptId);

            if ($attempt->status !== ExamAttemptStatus::InProgress) {
                return $attempt;
            }

            $now = now();
            $timeExpired = $now->greaterThanOrEqualTo($attempt->expires_at);

            if ($expired && ! $timeExpired) {
                throw ValidationException::withMessages([
                    'exam' => 'هنوز زمان آزمون به پایان نرسیده است.',
                ]);
            }

            $expired = $timeExpired;

            $correctAnswers = 0;

            foreach ($attempt->questions as $attemptQuestion) {
                $selectedKey = $attemptQuestion->selected_option_key;

                if ($attempt->exam->selection_mode === 'same') {
                    $sharedSnapshot = collect(
                        $attempt->exam->questions_snapshot ?? []
                    )->firstWhere(
                        'question_id',
                        $attemptQuestion->question_id,
                    );

                    $options = $sharedSnapshot['options'] ?? [];
                } else {
                    $options = $attemptQuestion->options_snapshot ?? [];
                }

                $correctOption = collect($options)
                    ->first(fn (array $option) => ($option['is_correct'] ?? false) === true);

                $isCorrect = $selectedKey !== null
                    && $correctOption !== null
                    && (string) $selectedKey === (string) ($correctOption['key'] ?? '');

                $attemptQuestion->forceFill([
                    'is_correct' => $isCorrect,
                ])->save();

                if ($isCorrect) {
                    $correctAnswers++;
                }
            }

            $questionCount = $attempt->questions->count();

            $score = $questionCount > 0
                ? round(($correctAnswers / $questionCount) * 100, 2)
                : 0;

            $attempt->forceFill([
                'status' => $expired
                    ? ExamAttemptStatus::Expired
                    : ExamAttemptStatus::Submitted,
                'submitted_at' => $now,
                'correct_answers' => $correctAnswers,
                'score_percentage' => $score,
                'passed' => $score >= $attempt->exam->pass_percentage,
            ])->save();

            return $attempt->refresh();
        }, attempts: 3);
    }
}
