<?php

use App\Enums\ExamAttemptStatus;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptQuestion;
use App\Services\Exams\FinishExamAttemptService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

new
#[Layout('layouts::app')]
class extends Component
{
    public ExamAttempt $attempt;

    public int $currentPosition = 1;

    public ?string $selectedOptionKey = null;

    public function mount(ExamAttempt $attempt): void
    {
        abort_unless($attempt->user_id === Auth::id(), 403);

        $this->attempt = $attempt->load([
            'exam',
            'questions',
        ]);

        if ($this->attempt->status !== ExamAttemptStatus::InProgress) {
            $this->redirectRoute('exam.show', ['exam' => $this->attempt->exam_id]);

            return;
        }

        if (now()->greaterThanOrEqualTo($this->attempt->expires_at)) {
            app(FinishExamAttemptService::class)->finish(
                $this->attempt->id,
                expired: true,
            );

            $this->redirectRoute('exam.show', ['exam' => $this->attempt->exam_id]);

            return;
        }

        $this->loadCurrentAnswer();
    }

    public function updatedCurrentPosition(): void
    {
        $this->currentPosition = max(
            1,
            min($this->currentPosition, $this->attempt->question_count),
        );

        $this->loadCurrentAnswer();
    }

    public function goToQuestion(int $position): void
    {
        $this->currentPosition = max(
            1,
            min($position, $this->attempt->question_count),
        );

        $this->loadCurrentAnswer();
    }

    public function saveAnswer(?string $optionKey): void
    {
        $result = DB::transaction(function () use ($optionKey): array {
            $attempt = ExamAttempt::query()
                ->whereKey($this->attempt->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->status !== ExamAttemptStatus::InProgress) {
                throw ValidationException::withMessages([
                    'answer' => 'این آزمون دیگر در حال برگزاری نیست.',
                ]);
            }

            if (now()->greaterThanOrEqualTo($attempt->expires_at)) {
                return [
                    'expired' => true,
                    'exam_id' => $attempt->exam_id,
                ];
            }

            $attemptQuestion = $attempt->questions()
                ->where('position', $this->currentPosition)
                ->lockForUpdate()
                ->firstOrFail();

            if ($optionKey !== null) {
                $options = $this->optionsFor($attemptQuestion);

                $validOption = collect($options)->contains(
                    fn (array $option): bool => isset($option['key'])
                        && hash_equals((string) $option['key'], $optionKey)
                );

                if (! $validOption) {
                    throw ValidationException::withMessages([
                        'answer' => 'گزینه انتخاب‌شده معتبر نیست.',
                    ]);
                }
            }

            $attemptQuestion->forceFill([
                'selected_option_key' => $optionKey,
                'answered_at' => $optionKey === null ? null : now(),
            ])->save();

            return [
                'expired' => false,
                'exam_id' => $attempt->exam_id,
                'selected_option_key' => $optionKey,
            ];
        }, attempts: 3);

        if ($result['expired']) {
            app(FinishExamAttemptService::class)->finish(
                $this->attempt->id,
                expired: true,
            );

            $this->redirectRoute('exam.show', [
                'exam' => $result['exam_id'],
            ]);

            return;
        }

        $this->selectedOptionKey = $result['selected_option_key'];

        // پاسخ‌های ذخیره‌شده را برای جابه‌جایی بین سؤال‌ها تازه‌سازی می‌کنیم.
        $this->attempt->unsetRelation('questions');
        $this->attempt->load('questions');
    }

    public function finish(FinishExamAttemptService $service): void
    {
        $attempt = ExamAttempt::query()
            ->whereKey($this->attempt->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $service->finish(
            $attempt->id,
            expired: now()->greaterThanOrEqualTo($attempt->expires_at),
        );

        $this->redirectRoute('exam.show', ['exam' => $attempt->exam_id]);
    }

    private function loadCurrentAnswer(): void
    {
        $attemptQuestion = $this->attempt->questions
            ->firstWhere('position', $this->currentPosition);

        $this->selectedOptionKey = $attemptQuestion?->selected_option_key;
    }

    private function optionsFor(ExamAttemptQuestion $attemptQuestion): array
    {
        if ($this->attempt->exam->selection_mode === 'same') {
            $snapshot = collect(
                $this->attempt->exam->questions_snapshot ?? []
            )->firstWhere('question_id', $attemptQuestion->question_id);

            return $snapshot['options'] ?? [];
        }

        return $attemptQuestion->options_snapshot ?? [];
    }

    public function with(): array
    {
        $attemptQuestion = $this->attempt->questions
            ->firstWhere('position', $this->currentPosition);

        if (! $attemptQuestion) {
            return [
                'attemptQuestion' => null,
                'questionData' => null,
                'options' => [],
                'remainingSeconds' => max(
                    0,
                    (int) now()->diffInSeconds($this->attempt->expires_at, false),
                ),
            ];
        }

        if ($this->attempt->exam->selection_mode === 'same') {
            $snapshot = collect(
                $this->attempt->exam->questions_snapshot ?? []
            )->firstWhere('question_id', $attemptQuestion->question_id);

            $questionData = $snapshot['question_snapshot'] ?? [];
            $rawOptions = $snapshot['options'] ?? [];
        } else {
            $questionData = $attemptQuestion->question_snapshot ?? [];
            $rawOptions = $attemptQuestion->options_snapshot ?? [];
        }

        $options = collect($rawOptions)
            ->map(fn (array $option) => [
                'key' => $option['key'],
                'text' => $option['text'] ?? '',
                'image_path' => $option['image_path'] ?? null,
                'dir' => $option['dir'] ?? 'rtl',
            ])
            ->values()
            ->all();

        return [
            'attemptQuestion' => $attemptQuestion,
            'questionData' => $questionData,
            'options' => $options,
            'remainingSeconds' => max(
                0,
                (int) now()->diffInSeconds($this->attempt->expires_at, false),
            ),
        ];
    }
};
