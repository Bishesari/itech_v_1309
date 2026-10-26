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

            $selectedQuestions = $this->selectQuestionsForExam($exam, $user);
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


            if ($exam->selection_mode === 'same') {
                $attemptQuestions = $selectedQuestions->shuffle()->values();

                foreach ($attemptQuestions as $index => $snapshot) {
                    ExamAttemptQuestion::query()->create([
                        'exam_attempt_id' => $attempt->id,
                        'question_id' => $snapshot['question_id'],
                        'position' => $index + 1,
                        'question_snapshot' => null,
                        'options_snapshot' => null,
                    ]);
                }
            } else {
                foreach ($selectedQuestions as $index => $question) {
                    $snapshot = $this->buildQuestionSnapshot($question);

                    ExamAttemptQuestion::query()->create([
                        'exam_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'position' => $index + 1,
                        'question_snapshot' => $snapshot['question_snapshot'],
                        'options_snapshot' => $snapshot['options'],
                    ]);
                }
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

    private function selectQuestionsForExam(
        Exam $exam,
        User $user,
    ): Collection {
        if ($exam->selection_mode === 'same') {
            $snapshot = $exam->questions_snapshot;

            if ($snapshot === null) {
                $questions = $this->selectEligibleQuestions($exam, null);

                $snapshot = $questions->map(
                    fn (Question $question) => $this->buildQuestionSnapshot($question)
                )->all();

                $exam->questions_snapshot = $snapshot;
                $exam->save();
            }

            if (count($snapshot) !== $exam->question_count) {
                throw ValidationException::withMessages([
                    'exam' => 'مجموعه سؤال‌های ثابت آزمون معتبر نیست.',
                ]);
            }

            return collect($snapshot);
        }

        if ($exam->selection_mode === 'different') {
            return $this->selectEligibleQuestions($exam, $user);
        }

        throw ValidationException::withMessages([
            'exam' => 'روش انتخاب سؤال‌های آزمون معتبر نیست.',
        ]);
    }


    private function selectEligibleQuestions(Exam $exam, ?User $user): Collection
    {
        $query = Question::query()
            ->where('is_active', true)
            ->where('type', QuestionType::MultipleChoice->value)
            ->whereHas('chapter', function (Builder $query) use ($exam) {
                $query->where('standard_id', $exam->standard_id);
            })
            ->with('options');

        if ($exam->type === ExamType::Quiz) {
            $chapterIds = $exam->chapters()->pluck('chapters.id');

            if ($chapterIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'exam' => 'برای آزمون کلاسی فصلی انتخاب نشده است.',
                ]);
            }

            $query->whereIn('chapter_id', $chapterIds);
        } else {
            $query->where('is_frequently_final', true);
        }

        $eligibleQuestions = $query->get();

        if ($eligibleQuestions->count() < $exam->question_count) {
            throw ValidationException::withMessages([
                'exam' => 'تعداد سؤال‌های واجد شرایط برای این آزمون کافی نیست.',
            ]);
        }

        $usedQuestionIds = [];

        if ($user !== null) {
            $usedQuestionIds = Question::query()
                ->whereHas('attemptQuestions.attempt', function (Builder $query) use ($exam, $user) {
                    $query->where('exam_id', $exam->id)
                        ->where('user_id', $user->id);
                })
                ->pluck('questions.id')
                ->all();
        }

        return $this->selectQuestionsProportionally(
            $eligibleQuestions,
            (int) $exam->question_count,
            $usedQuestionIds,
        );
    }

    /**
     * انتخاب متناسب با تعداد سؤال‌های واجد شرایط هر فصل.
     */
    private function selectQuestionsProportionally(
        Collection $questions,
        int $requiredCount,
        array $usedQuestionIds = [],
    ): Collection {
        $chapters = $questions->groupBy('chapter_id');

        $totalAvailable = $questions->count();

        if ($requiredCount < 1 || $totalAvailable < $requiredCount) {
            throw ValidationException::withMessages([
                'exam' => 'تعداد سؤال‌های واجد شرایط برای این آزمون کافی نیست.',
            ]);
        }

        $quotas = [];
        $fractions = [];
        $assigned = 0;

        foreach ($chapters as $chapterId => $chapterQuestions) {
            $exactQuota = $requiredCount
                * $chapterQuestions->count()
                / $totalAvailable;

            $quota = min(
                (int) floor($exactQuota),
                $chapterQuestions->count(),
            );

            $quotas[$chapterId] = $quota;
            $fractions[$chapterId] = $exactQuota - floor($exactQuota);
            $assigned += $quota;
        }

        // توزیع باقیمانده بر اساس بزرگ‌ترین بخش اعشاری،
        // بدون عبور از موجودی هیچ فصل.
        arsort($fractions);

        while ($assigned < $requiredCount) {
            $progress = false;

            foreach (array_keys($fractions) as $chapterId) {
                if ($assigned >= $requiredCount) {
                    break;
                }

                if ($quotas[$chapterId] >= $chapters[$chapterId]->count()) {
                    continue;
                }

                $quotas[$chapterId]++;
                $assigned++;
                $progress = true;
            }

            if (! $progress) {
                throw ValidationException::withMessages([
                    'exam' => 'توزیع سؤال‌ها بین فصل‌ها امکان‌پذیر نیست.',
                ]);
            }
        }

        $selected = collect();

        foreach ($chapters as $chapterId => $chapterQuestions) {
            $quota = $quotas[$chapterId];

            if ($quota === 0) {
                continue;
            }

            $unused = $chapterQuestions
                ->reject(fn (Question $question) => in_array(
                    $question->id,
                    $usedQuestionIds,
                ))
                ->shuffle();

            $chapterSelected = $unused->take($quota);

            if ($chapterSelected->count() < $quota) {
                $remaining = $quota - $chapterSelected->count();

                $used = $chapterQuestions
                    ->filter(fn (Question $question) => in_array(
                        $question->id,
                        $usedQuestionIds,
                    ))
                    ->shuffle()
                    ->take($remaining);

                $chapterSelected = $chapterSelected->concat($used);
            }

            $selected = $selected->concat($chapterSelected);
        }

        if ($selected->count() !== $requiredCount) {
            throw ValidationException::withMessages([
                'exam' => 'تعداد سؤال‌های انتخاب‌شده با تعداد درخواستی برابر نیست.',
            ]);
        }

        return $selected->shuffle()->values();
    }


    private function buildQuestionSnapshot(Question $question): array
    {
        $options = $question->options->shuffle()->values();

        if (
            $options->count() !== 4
            || $options->where('is_correct', true)->count() !== 1
        ) {
            throw ValidationException::withMessages([
                'exam' => 'یکی از سؤال‌های انتخاب‌شده چهار گزینه معتبر و دقیقاً یک پاسخ صحیح ندارد.',
            ]);
        }

        return [
            'question_id' => $question->id,
            'question_snapshot' => [
                'text' => $question->text,
                'image_path' => $question->image_path,
                'code' => $question->code,
                'code_language' => $question->code_language?->value,
                'explanation' => $question->explanation,
            ],
            'options' => $options->map(fn ($option) => [
                'key' => (string) Str::uuid(),
                'text' => $option->text,
                'image_path' => $option->image_path,
                'dir' => $option->dir,
                'is_correct' => $option->is_correct,
            ])->all(),
        ];
    }

}
