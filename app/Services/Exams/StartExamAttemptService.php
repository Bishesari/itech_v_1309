<?php

namespace App\Services\Exams;

use App\Enums\ExamAttemptStatus;
use App\Enums\ExamType;
use App\Enums\ProductType;
use App\Enums\QuestionType;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptQuestion;
use App\Models\OrderItem;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StartExamAttemptService
{
    public function start(
        int $examId,
        User $user,
        ?int $orderItemId = null,
    ): ExamAttempt {
        return DB::transaction(function () use (
            $examId,
            $user,
            $orderItemId,
        ) {
            // قفل رکورد آزمون، شروع‌های هم‌زمان را برای این آزمون
            // به ترتیب اجرا می‌کند.
            $exam = Exam::query()
                ->lockForUpdate()
                ->findOrFail($examId);

            $this->validateExamAvailability($exam);

            if ($exam->type === ExamType::Simulated) {
                $this->validatePurchase($exam, $user, $orderItemId);
            } else {
                if ($orderItemId !== null) {
                    throw ValidationException::withMessages([
                        'exam' => 'برای این آزمون رایگان، سفارش لازم نیست.',
                    ]);
                }
            }

            // اگر نوبت قبلی هنوز فعال است، اجازه شروع مجدد نده.
            $activeAttempt = ExamAttempt::query()
                ->where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->where('status', ExamAttemptStatus::InProgress->value)
                ->lockForUpdate()
                ->first();

            if ($activeAttempt) {
                if ($activeAttempt->expires_at->isPast()) {
                    // پایان و تصحیح نوبت منقضی‌شده را سرویس پایان آزمون
                    // انجام خواهد داد؛ تا آن زمان نوبت جدید نساز.
                    throw ValidationException::withMessages([
                        'exam' => 'زمان نوبت قبلی تمام شده است؛ ابتدا باید نتیجه آن ثبت شود.',
                    ]);
                }

                throw ValidationException::withMessages([
                    'exam' => 'یک نوبت آزمون فعال دارید؛ ابتدا همان را ادامه دهید.',
                ]);
            }

            $this->validateAttemptLimit($exam, $user, $orderItemId);

            $selectedQuestions = $this->selectQuestions($exam, $user);

            $startedAt = now();
            $duration = $exam->durationInSeconds();

            $attempt = ExamAttempt::query()->create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'order_item_id' => $orderItemId,
                'status' => ExamAttemptStatus::InProgress,
                'started_at' => $startedAt,
                'expires_at' => $startedAt->copy()->addSeconds($duration),
                'question_count' => $selectedQuestions->count(),
                'duration_seconds' => $duration,
            ]);

            foreach ($selectedQuestions as $index => $question) {
                $options = $question->options->shuffle()->values();

                $correctCount = $options->where('is_correct', true)->count();

                if ($options->count() !== 4 || $correctCount !== 1) {
                    throw ValidationException::withMessages([
                        'exam' => 'یکی از سؤال‌های انتخاب‌شده چهار گزینه معتبر و دقیقاً یک پاسخ صحیح ندارد.',
                    ]);
                }

                $optionSnapshots = $options->map(function ($option) {
                    return [
                        'key' => (string) Str::uuid(),
                        'text' => $option->text,
                        'image_path' => $option->image_path,
                        'dir' => $option->dir,
                        'is_correct' => $option->is_correct,
                    ];
                });

                ExamAttemptQuestion::query()->create([
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'position' => $index + 1,
                    'question_snapshot' => [
                        'text' => $question->text,
                        'image_path' => $question->image_path,
                        'code' => $question->code,
                        'code_language' => $question->code_language?->value,
                        'explanation' => $question->explanation,
                    ],
                    'options_snapshot' => $optionSnapshots->all(),
                ]);
            }

            return $attempt;
        }, attempts: 3);
    }

    private function validateExamAvailability(Exam $exam): void
    {
        if (! $exam->is_active) {
            throw ValidationException::withMessages([
                'exam' => 'این آزمون فعال نیست.',
            ]);
        }

        if ($exam->starts_at && now()->lt($exam->starts_at)) {
            throw ValidationException::withMessages([
                'exam' => 'زمان شروع این آزمون هنوز نرسیده است.',
            ]);
        }

        if ($exam->ends_at && now()->gt($exam->ends_at)) {
            throw ValidationException::withMessages([
                'exam' => 'مهلت شرکت در این آزمون به پایان رسیده است.',
            ]);
        }

        if (
            $exam->question_count < 1
            || $exam->seconds_per_question < 1
        ) {
            throw ValidationException::withMessages([
                'exam' => 'تنظیمات تعداد سؤال یا زمان آزمون معتبر نیست.',
            ]);
        }
    }

    private function validatePurchase(
        Exam $exam,
        User $user,
        ?int $orderItemId,
    ): void {
        if (! $orderItemId || ! $exam->product_id) {
            throw ValidationException::withMessages([
                'exam' => 'برای شروع این آزمون، خرید معتبر لازم است.',
            ]);
        }

        $item = OrderItem::query()
            ->with(['order.payments'])
            ->lockForUpdate()
            ->find($orderItemId);

        if (
            ! $item
            || ! $item->order
            || (int) $item->order->user_id !== (int) $user->id
            || $item->order->status !== 'paid'
            || (int) $item->product_id !== (int) $exam->product_id
            || $item->product_type !== ProductType::SimulatedExam->value
            || (int) $item->quantity !== 1
            || ! $item->order->payments->contains(
                fn ($payment) => $payment->isPaid()
            )
        ) {
            throw ValidationException::withMessages([
                'exam' => 'خرید این آزمون معتبر نیست یا پرداخت آن تأیید نشده است.',
            ]);
        }
    }

    private function validateAttemptLimit(
        Exam $exam,
        User $user,
        ?int $orderItemId,
    ): void {
        if ($exam->type === ExamType::Simulated) {
            // order_item_id در دیتابیس یکتا است؛ هر خرید فقط یک نوبت.
            $alreadyUsed = ExamAttempt::query()
                ->where('order_item_id', $orderItemId)
                ->exists();

            if ($alreadyUsed) {
                throw ValidationException::withMessages([
                    'exam' => 'این خرید قبلاً برای ایجاد نوبت آزمون استفاده شده است.',
                ]);
            }

            return;
        }

        $maxAttempts = $exam->max_attempts ?? 2;

        $attemptCount = ExamAttempt::query()
            ->where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        if ($attemptCount >= $maxAttempts) {
            throw ValidationException::withMessages([
                'exam' => 'تعداد دفعات مجاز شرکت در این آزمون به پایان رسیده است.',
            ]);
        }
    }

    private function selectQuestions(
        Exam $exam,
        User $user,
    ): Collection {
        $baseQuery = Question::query()
            ->where('is_active', true)
            ->where('type', QuestionType::MultipleChoice->value)
            ->whereHas('chapter', function (Builder $query) use ($exam) {
                $query->where('standard_id', $exam->standard_id);
            })
            ->with('options');

        if (
            $exam->type === ExamType::Midterm
            || $exam->type === ExamType::Simulated
        ) {
            $baseQuery->where('is_frequently_final', true);
        }

        $usedQuestionIds = Question::query()
            ->whereHas('attemptQuestions.attempt', function (Builder $query) use (
                $exam,
                $user,
            ) {
                $query->where('exam_id', $exam->id)
                    ->where('user_id', $user->id);
            })
            ->select('questions.id');

        if ($exam->selection_mode === 'blueprint') {
            $rules = $exam->blueprintRules()->with('chapter')->get();

            if ($rules->isEmpty()) {
                throw ValidationException::withMessages([
                    'exam' => 'طرح انتخاب سؤال‌ها تعریف نشده است.',
                ]);
            }

            if ((int) $rules->sum('question_count') !== $exam->question_count) {
                throw ValidationException::withMessages([
                    'exam' => 'مجموع تعداد سؤال‌های طرح با تعداد سؤال‌های آزمون برابر نیست.',
                ]);
            }

            $selected = collect();

            foreach ($rules as $rule) {
                if (
                    ! $rule->chapter
                    || (int) $rule->chapter->standard_id !== (int) $exam->standard_id
                ) {
                    throw ValidationException::withMessages([
                        'exam' => 'یکی از فصل‌های طرح متعلق به استاندارد این آزمون نیست.',
                    ]);
                }

                $query = clone $baseQuery;

                $query->where('chapter_id', $rule->chapter_id)
                    ->where('difficulty', $rule->difficulty->value);

                $selected = $selected->concat(
                    $this->pickQuestions(
                        $query,
                        (int) $rule->question_count,
                        $usedQuestionIds,
                    )->all()
                );
            }
        } elseif ($exam->selection_mode === 'random') {
            $selected = $this->pickQuestions(
                clone $baseQuery,
                $exam->question_count,
                $usedQuestionIds,
            );
        } else {
            throw ValidationException::withMessages([
                'exam' => 'روش انتخاب سؤال‌های آزمون معتبر نیست.',
            ]);
        }

        if ($selected->count() !== $exam->question_count) {
            throw ValidationException::withMessages([
                'exam' => 'تعداد سؤال‌های واجد شرایط برای برگزاری این آزمون کافی نیست.',
            ]);
        }

        return $selected->values();
    }

    private function pickQuestions(
        Builder $query,
        int $count,
        Builder $usedQuestionIds,
    ): Collection {
        // ابتدا سؤال‌هایی را انتخاب می‌کنیم که کاربر در نوبت‌های قبلی
        // همین آزمون ندیده است.
        $unused = (clone $query)
            ->whereNotIn('questions.id', $usedQuestionIds)
            ->get()
            ->shuffle();

        if ($unused->count() >= $count) {
            return $unused->take($count)->values();
        }

        // اگر سؤال جدید کافی نبود، از سؤال‌های قبلی هم استفاده می‌کنیم.
        $used = $query
            ->whereIn('questions.id', $usedQuestionIds)
            ->get()
            ->shuffle();

        return $unused
            ->concat($used->all())
            ->take($count)
            ->values();
    }
}
