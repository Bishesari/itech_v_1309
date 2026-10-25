<?php

use App\Enums\ExamType;
use App\Enums\ProductType;
use App\Models\Exam;
use App\Models\Product;
use App\Models\Standard;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?int $examId = null;

    public string $title = '';

    public ?int $standard_id = null;

    public string $type = 'quiz';

    public string $description = '';

    public int $question_count = 20;

    public string $selection_mode = 'blueprint';

    public int $seconds_per_question = 90;

    public ?int $pass_percentage = null;

    public ?int $max_attempts = null;

    public ?int $product_id = null;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public bool $is_active = false;

    #[On('edit-exam')]
    public function loadExam(int $examId): void
    {
        $exam = Exam::findOrFail($examId);

        $this->examId = $exam->id;
        $this->title = $exam->title;
        $this->standard_id = $exam->standard_id;
        $this->type = $exam->type->value;
        $this->description = $exam->description ?? '';
        $this->question_count = $exam->question_count;
        $this->selection_mode = $exam->selection_mode;
        $this->seconds_per_question = $exam->seconds_per_question;
        $this->pass_percentage = $exam->pass_percentage;
        $this->max_attempts = $exam->max_attempts;
        $this->product_id = $exam->product_id;
        $this->starts_at = $exam->starts_at?->format('Y-m-d\TH:i');
        $this->ends_at = $exam->ends_at?->format('Y-m-d\TH:i');
        $this->is_active = $exam->is_active;

        $this->resetValidation();

        Flux::modal('edit-exam')->show();
    }

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

        $exam = Exam::findOrFail($this->examId);

        if ($this->type !== ExamType::Simulated->value) {
            $validated['product_id'] = null;
        }

        $exam->update($validated);

        $this->dispatch('exam-updated', examId: $exam->id);

        Flux::modal('edit-exam')->close();

        Flux::toast(
            heading: 'آزمون ویرایش شد',
            text: 'تغییرات با موفقیت ذخیره شدند.',
            variant: 'success',
        );

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
