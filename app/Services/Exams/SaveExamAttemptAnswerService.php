<?php

namespace App\Services\Exams;

use App\Enums\ExamAttemptStatus;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveExamAttemptAnswerService
{
    public function __construct(
        private readonly SubmitExamAttemptService $submitService,
    ) {}

    public function save(
        int $attemptId,
        int $attemptQuestionId,
        User $user,
        ?string $selectedOptionKey,
    ): ExamAttemptQuestion {
        $result = DB::transaction(function () use (
            $attemptId,
            $attemptQuestionId,
            $user,
            $selectedOptionKey,
        ) {
            $attempt = ExamAttempt::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($attemptId);

            if ($attempt->status !== ExamAttemptStatus::InProgress) {
                throw ValidationException::withMessages([
                    'exam' => 'این آزمون دیگر قابل پاسخ‌گویی نیست.',
                ]);
            }

            if ($attempt->expires_at->isPast()) {
                return ['expired' => true];
            }

            $question = ExamAttemptQuestion::query()
                ->where('exam_attempt_id', $attempt->id)
                ->whereKey($attemptQuestionId)
                ->firstOrFail();

            if ($selectedOptionKey !== null) {
                $optionExists = collect($question->options_snapshot)
                    ->contains(
                        fn (array $option) =>
                            ($option['key'] ?? null) === $selectedOptionKey
                    );

                if (! $optionExists) {
                    throw ValidationException::withMessages([
                        'answer' => 'گزینه انتخاب‌شده معتبر نیست.',
                    ]);
                }
            }

            $question->update([
                'selected_option_key' => $selectedOptionKey,
                'answered_at' => $selectedOptionKey === null ? null : now(),
            ]);

            return ['question' => $question->refresh()];
        }, attempts: 3);

        if ($result['expired'] ?? false) {
            $this->submitService->submit($attemptId, $user);

            throw ValidationException::withMessages([
                'exam' => 'زمان آزمون به پایان رسیده و پاسخ‌های ذخیره‌شده نهایی شده‌اند.',
            ]);
        }

        return $result['question'];
    }
}
