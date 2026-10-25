<?php

use App\Enums\ExamType;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Chapter;
use App\Models\Exam;
use App\Models\ExamBlueprintRule;
use App\Models\Question;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?int $examId = null;

    public ?int $standardId = null;

    public string $examTitle = '';

    public int $questionCount = 0;

    public string $examType = '';

    public array $rules = [];

    #[On('manage-exam-blueprint')]
    public function loadBlueprint(int $examId): void
    {
        $exam = Exam::with('blueprintRules')->findOrFail($examId);

        if ($exam->selection_mode !== 'blueprint') {
            Flux::toast(
                heading: 'روش انتخاب سؤال‌ها',
                text: 'ابتدا روش انتخاب سؤال‌ها را روی «بر اساس الگو» قرار دهید.',
                variant: 'warning',
            );

            return;
        }

        $this->examId = $exam->id;
        $this->standardId = $exam->standard_id;
        $this->examTitle = $exam->title;
        $this->questionCount = $exam->question_count;
        $this->examType = $exam->type->value;

        $this->rules = $exam->blueprintRules
            ->map(fn (ExamBlueprintRule $rule) => [
                'chapter_id' => (string) $rule->chapter_id,
                'difficulty' => $rule->difficulty->value,
                'question_count' => (int) $rule->question_count,
            ])
            ->values()
            ->all();

        if (empty($this->rules)) {
            $firstChapterId = Chapter::query()
                ->where('standard_id', $exam->standard_id)
                ->orderBy('number')
                ->value('id');

            $this->rules = [[
                'chapter_id' => $firstChapterId ? (string) $firstChapterId : '',
                'difficulty' => QuestionDifficulty::Easy->value,
                'question_count' => 1,
            ]];
        }

        $this->resetValidation();

        Flux::modal('exam-blueprint')->show();
    }

    public function addRule(): void
    {
        $this->rules[] = [
            'chapter_id' => '',
            'difficulty' => QuestionDifficulty::Easy->value,
            'question_count' => 1,
        ];
    }

    public function removeRule(int $index): void
    {
        if (! array_key_exists($index, $this->rules)) {
            return;
        }

        unset($this->rules[$index]);

        $this->rules = array_values($this->rules);

        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'rules' => ['required', 'array', 'min:1'],
            'rules.*.chapter_id' => [
                'required',
                'integer',
                Rule::exists('chapters', 'id')
                    ->where('standard_id', $this->standardId),
            ],
            'rules.*.difficulty' => [
                'required',
                Rule::enum(QuestionDifficulty::class),
            ],
            'rules.*.question_count' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function save(): void
    {
        $exam = Exam::findOrFail($this->examId);

        if ($exam->selection_mode !== 'blueprint') {
            $this->addError('rules', 'روش انتخاب سؤال‌های آزمون تغییر کرده است.');

            return;
        }

        $this->standardId = $exam->standard_id;
        $this->questionCount = $exam->question_count;
        $this->examType = $exam->type->value;

        $validated = $this->validate();

        $rules = $validated['rules'];

        if (array_sum(array_column($rules, 'question_count')) !== $exam->question_count) {
            $this->addError(
                'rules',
                'مجموع تعداد سؤال‌های الگو باید دقیقاً برابر تعداد سؤال‌های آزمون باشد.',
            );

            return;
        }

        $combinations = [];

        foreach ($rules as $index => $rule) {
            $key = $rule['chapter_id'].':'.$rule['difficulty'];

            if (isset($combinations[$key])) {
                $this->addError(
                    "rules.{$index}.difficulty",
                    'ترکیب این فصل و سطح دشواری تکراری است.',
                );

                return;
            }

            $combinations[$key] = true;
        }

        $chapterIds = array_unique(array_map(
            fn (array $rule) => (int) $rule['chapter_id'],
            $rules,
        ));

        if ($exam->type !== ExamType::Quiz) {
            $allChapterIds = Chapter::query()
                ->where('standard_id', $exam->standard_id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (array_diff($allChapterIds, $chapterIds) !== []) {
                $this->addError(
                    'rules',
                    'برای آزمون میان‌ترم و شبیه‌سازی باید از تمام فصل‌های استاندارد سؤال در نظر گرفته شود.',
                );

                return;
            }
        }

        foreach ($rules as $index => $rule) {
            $query = Question::query()
                ->where('chapter_id', (int) $rule['chapter_id'])
                ->where('type', QuestionType::MultipleChoice->value)
                ->where('difficulty', $rule['difficulty'])
                ->where('is_active', true);

            if ($exam->type !== ExamType::Quiz) {
                $query->where('is_frequently_final', true);
            }

            $availableCount = $query->count();

            if ($availableCount < (int) $rule['question_count']) {
                $this->addError(
                    "rules.{$index}.question_count",
                    "برای این فصل و سطح دشواری فقط {$availableCount} سؤال واجد شرایط وجود دارد.",
                );

                return;
            }
        }

        DB::transaction(function () use ($exam, $rules): void {
            ExamBlueprintRule::query()
                ->where('exam_id', $exam->id)
                ->delete();

            foreach ($rules as $rule) {
                ExamBlueprintRule::create([
                    'exam_id' => $exam->id,
                    'chapter_id' => (int) $rule['chapter_id'],
                    'difficulty' => $rule['difficulty'],
                    'question_count' => (int) $rule['question_count'],
                ]);
            }
        });

        Flux::modal('exam-blueprint')->close();

        Flux::toast(
            heading: 'الگوی آزمون ذخیره شد',
            text: 'قواعد انتخاب سؤال‌ها با موفقیت ذخیره شدند.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        return [
            'chapters' => $this->standardId
                ? Chapter::query()
                    ->where('standard_id', $this->standardId)
                    ->orderBy('number')
                    ->get()
                : collect(),

            'difficulties' => QuestionDifficulty::cases(),

            'requiredChapterCoverage' => $this->examType !== ''
                && $this->examType !== ExamType::Quiz->value,
        ];
    }
};
