<?php

use App\Enums\ExamType;
use App\Enums\ProductType;
use App\Models\Exam;
use App\Models\Product;
use App\Models\Standard;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Flux\Flux;

new class extends Component
{
    public string $title = '';

    public ?int $standard_id = null;

    public string $type = 'quiz';

    public string $description = '';

    public int $question_count = 20;

    public string $selection_mode = 'random';

    public int $seconds_per_question = 90;

    public ?int $pass_percentage = null;

    public ?int $max_attempts = null;

    public ?int $product_id = null;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public bool $is_active = false;

    public function updatedType(): void
    {
        if ($this->type !== ExamType::Simulated->value) {
            $this->product_id = null;
        }
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'standard_id' => ['required', 'integer', 'exists:standards,id'],
            'type' => ['required', Rule::enum(ExamType::class)],
            'description' => ['nullable', 'string'],
            'question_count' => ['required', 'integer', 'min:1'],
            'selection_mode' => ['required', Rule::in(['random', 'blueprint'])],
            'seconds_per_question' => ['required', 'integer', 'min:1', 'max:3600'],
            'pass_percentage' => ['nullable', 'integer', 'between:0,100'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'product_id' => [
                Rule::requiredIf($this->type === ExamType::Simulated->value),
                'nullable',
                'integer',
                Rule::exists('products', 'id')
                    ->where('type', ProductType::SimulatedExam->value)
                    ->where('is_active', true),
            ],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->type !== ExamType::Simulated->value) {
            $validated['product_id'] = null;
        }

        $exam = Exam::create($validated);

        $this->dispatch('exam-created', examId: $exam->id);

        Flux::modal('create-exam')->close();

        Flux::toast(
            heading: 'آزمون ایجاد شد',
            text: 'اطلاعات آزمون با موفقیت ثبت شد.',
            variant: 'success',
        );

        $this->reset([
            'title',
            'standard_id',
            'description',
            'product_id',
            'starts_at',
            'ends_at',
            'pass_percentage',
            'max_attempts',
        ]);

        $this->type = ExamType::Quiz->value;
        $this->question_count = 20;
        $this->selection_mode = 'random';
        $this->seconds_per_question = 90;
        $this->is_active = false;

        $this->resetValidation();
    }

    public function with(): array
    {
        return [
            'standards' => Standard::query()
                ->orderBy('name_fa')
                ->get(),

            'products' => Product::query()
                ->where('type', ProductType::SimulatedExam->value)
                ->where('is_active', true)
                ->orderBy('title')
                ->get(),

            'examTypes' => ExamType::cases(),
        ];
    }
};
