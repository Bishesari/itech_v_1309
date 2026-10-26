<?php

use App\Enums\ExamType;
use App\Enums\ProductType;
use App\Enums\QuestionType;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Standard;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Models\Product;

new class extends Component
{
    public string $exam_type = '';

    public ?int $standard_id = null;

    public array $selected_chapters = [];

    public string $title = '';

    public ?int $question_count = null;

    public int $seconds_per_question = 90;

    public ?int $pass_percentage = null;

    public string $description = '';

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public string $selection_mode = 'different';

    public ?int $product_id = null;

    protected function rules(): array
    {
        $rules = [
            'exam_type' => [
                'required',
                Rule::enum(ExamType::class),
            ],

            'standard_id' => [
                'required',
                'integer',
                'exists:standards,id',
            ],

            'title' => [
                'required',
                'string',
                'max:200',
            ],

            'question_count' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            'seconds_per_question' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            'pass_percentage' => [
                'nullable',
                'integer',
                'between:0,100',
            ],

            'description' => [
                'nullable',
                'string',
            ],
            'selection_mode' => [
                'required',
                'in:different,same',
            ],
        ];

        if ($this->exam_type === ExamType::Quiz->value) {
            $rules['selected_chapters'] = [
                'required',
                'array',
                'min:1',
            ];

            $rules['selected_chapters.*'] = [
                'required',
                'integer',
                'distinct',
                Rule::exists('chapters', 'id')
                    ->where('standard_id', $this->standard_id),
            ];
        }

        if ($this->exam_type !== ExamType::Simulated->value) {
            $rules['starts_at'] = [
                'required',
                'date',
            ];

            $rules['ends_at'] = [
                'required',
                'date',
                'after:starts_at',
            ];
        }

        $rules['question_count'][] = function ($attribute, $value, $fail) {
            if (
                is_numeric($value)
                && (int) $value > $this->totalQuestions
            ) {
                $fail(
                    'تعداد سؤال‌های آزمون نمی‌تواند از تعداد سؤال‌های قابل استفاده بیشتر باشد.'
                );
            }
        };

        if ($this->exam_type === ExamType::Simulated->value) {
            $rules['product_id'] = [
                'required',
                'integer',
                Rule::exists('products', 'id')
                    ->where('type', ProductType::SimulatedExam->value)
                    ->where('is_active', true),
            ];
        }

        return $rules;
    }

    public function updatedStandardId(): void
    {
        $this->selected_chapters = [];
    }

    public function updatedExamType(): void
    {
        if ($this->exam_type !== ExamType::Simulated->value) {
            $this->product_id = null;
            $this->resetValidation('product_id');
        }

        if ($this->exam_type === ExamType::Simulated->value) {
            $this->starts_at = null;
            $this->ends_at = null;
        }

        if ($this->exam_type !== ExamType::Quiz->value) {
            $this->selected_chapters = [];
        }
    }

    public function getTotalQuestionsProperty(): int
    {
        if (! $this->standard_id) {
            return 0;
        }

        $query = Question::query()
            ->whereHas('chapter', fn ($query) => $query->where('standard_id', $this->standard_id)
            )
            ->where('is_active', true)
            ->where('type', QuestionType::MultipleChoice->value);

        if ($this->exam_type === ExamType::Quiz->value) {
            if (empty($this->selected_chapters)) {
                return 0;
            }

            $query->whereIn('chapter_id', $this->selected_chapters);
        } else {
            $query->where('is_frequently_final', true);
        }

        return $query->count();
    }

    public function with(): array
    {
        return [
            'examTypes' => ExamType::cases(),

            'standards' => Standard::query()
                ->orderBy('name_fa')
                ->get(),

            'products' => Product::query()
                ->where('type', ProductType::SimulatedExam->value)
                ->where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title']),

            'chapters' => $this->standard_id
                ? Standard::find($this->standard_id)
                    ?->chapters()
                    ->withCount([
                        'questions as eligible_questions_count' => fn ($query) => $query
                            ->where('is_active', true)
                            ->where('type', QuestionType::MultipleChoice->value),
                    ])
                    ->get() ?? collect()
                : collect(),
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        if ($validated['question_count'] > $this->totalQuestions) {
            $this->addError(
                'question_count',
                'تعداد سؤال‌های آزمون از تعداد سؤال‌های قابل استفاده بیشتر است.'
            );

            return;
        }

        DB::transaction(function () use ($validated) {
            $exam = Exam::create([
                'standard_id' => $validated['standard_id'],
                'product_id' => $validated['exam_type'] === ExamType::Simulated->value
                    ? (int) $validated['product_id']
                    : null,
                'title' => $validated['title'],
                'type' => $validated['exam_type'],
                'description' => $validated['description'] ?: null,
                'question_count' => $validated['question_count'],
                'selection_mode' => $validated['selection_mode'],
                'seconds_per_question' => $validated['seconds_per_question'],
                'pass_percentage' => $validated['pass_percentage'] ?? null,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'is_active' => false,
            ]);

            if ($validated['exam_type'] === ExamType::Quiz->value) {
                $exam->chapters()->sync($validated['selected_chapters']);
            }
        });

        $this->dispatch('notify', message: 'آزمون با موفقیت ایجاد شد.');
    }
};
