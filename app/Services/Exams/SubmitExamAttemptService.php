<?php

namespace App\Services\Exams;

use App\Enums\ExamAttemptStatus;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitExamAttemptService
{
    public function submit(int $attemptId, User $user): ExamAttempt
    {
        return DB::transaction(function () use ($attemptId, $user) {
            $attempt = ExamAttempt::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($attemptId);

            // اگر قبلاً نهایی شده، همان نتیجه را برگردان.
            if ($attempt->status !== ExamAttemptStatus::InProgress) {
                return $attempt;
            }

            $questions = ExamAttemptQuestion::query()
                ->where('exam_attempt_id', $attempt->id)
                ->orderBy('position')
                ->lockForUpdate()
                ->get();

            if (
                $questions->isEmpty()
                || $questions->count() !== $attempt->question_count
            ) {
                throw ValidationException::withMessages([
                    'exam' => 'ساختار پاسخ‌های آزمون معتبر نیست.',
                ]);
            }

            $correctAnswers = 0;

            foreach ($questions as $question) {
                $selectedOption = null;

                if ($question->selected_option_key !== null) {
                    $selectedOption = collect($question->options_snapshot)
                        ->firstWhere('key', $question->selected_option_key);
                }

                $isCorrect = $selectedOption !== null
                    && ($selectedOption['is_correct'] ?? false) === true;

                if ($isCorrect) {
                    $correctAnswers++;
                }

                $question->update([
                    'is_correct' => $isCorrect,
                ]);
            }

            $scorePercentage = round(
                ($correctAnswers / $questions->count()) * 100,
                2
            );

            $exam = $attempt->exam()->firstOrFail();

            $isExpired = $attempt->expires_at->isPast();

            $attempt->update([
                'status' => $isExpired
                    ? ExamAttemptStatus::Expired
                    : ExamAttemptStatus::Submitted,

                'submitted_at' => now(),
                'correct_answers' => $correctAnswers,
                'score_percentage' => $scorePercentage,

                'passed' => $exam->pass_percentage === null
                    ? null
                    : $scorePercentage >= $exam->pass_percentage,
            ]);

            return $attempt->refresh();
        }, attempts: 3);
    }
}
