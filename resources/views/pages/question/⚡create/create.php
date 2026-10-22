<?php

use App\Enums\CodeLanguage;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Option;
use App\Models\Question;
use App\Models\Standard;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

new
#[Title('ثبت سؤال جدید')]
class extends Component
{
    use WithFileUploads;

    #[Url(as: 'field_id')]
    public string $fieldId = '';

    #[Url(as: 'standard_id')]
    public string $standardId = '';

    #[Url(as: 'chapter_id')]
    public string $chapterId = '';

    public string $type = 'multiple_choice';

    public string $text = '';

    public string $explanation = '';

    #[Computed]
    public function codeLanguages(): array
    {
        return CodeLanguage::cases();
    }

    public string $difficulty = 'medium';

    public $questionImage = null;

    public string $code = '';

    public ?string $codeLanguage = null;

    public bool $isActive = true;

    public bool $isFrequentlyFinal = false;

    public array $options = [
        ['text' => '', 'image' => null, 'dir' => 'rtl', 'is_correct' => false],
        ['text' => '', 'image' => null, 'dir' => 'rtl', 'is_correct' => false],
        ['text' => '', 'image' => null, 'dir' => 'rtl', 'is_correct' => false],
        ['text' => '', 'image' => null, 'dir' => 'rtl', 'is_correct' => false],
    ];

    public function mount(): void
    {
        // مقادیر فیلتر از query string دریافت می‌شوند.
        // فقط مقادیر معتبر را نگه می‌داریم.

        if (
            $this->fieldId !== ''
            && ! Field::whereKey($this->fieldId)->exists()
        ) {
            $this->fieldId = '';
        }

        if (
            $this->standardId !== ''
            && ! Standard::whereKey($this->standardId)
                ->where('field_id', $this->fieldId)
                ->exists()
        ) {
            $this->standardId = '';
        }

        if (
            $this->chapterId !== ''
            && ! Chapter::whereKey($this->chapterId)
                ->where('standard_id', $this->standardId)
                ->exists()
        ) {
            $this->chapterId = '';
        }
    }

    #[Computed]
    public function fields(): Collection
    {
        return Field::query()
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    #[Computed]
    public function standards(): Collection
    {
        return Standard::query()
            ->when(
                $this->fieldId !== '',
                fn ($query) => $query->where('field_id', $this->fieldId)
            )
            ->orderBy('name_fa')
            ->get(['id', 'field_id', 'code', 'name_fa']);
    }

    #[Computed]
    public function chapters(): Collection
    {
        if ($this->standardId === '') {
            return collect();
        }

        return Chapter::query()
            ->where('standard_id', $this->standardId)
            ->orderBy('number')
            ->get(['id', 'standard_id', 'number', 'title']);
    }

    public function updatedFieldId(): void
    {
        $this->standardId = '';
        $this->chapterId = '';

        unset($this->standards, $this->chapters);

        $this->resetValidation();
    }

    public function updatedStandardId(): void
    {
        $this->chapterId = '';

        unset($this->chapters);

        $this->resetValidation();
    }

    public function updatedType(): void
    {
        $this->resetValidation();
    }

    protected function rules(): array
    {
        $rules = [
            'fieldId' => ['required', 'integer', 'exists:fields,id'],

            'standardId' => [
                'required',
                'integer',
                Rule::exists('standards', 'id')
                    ->where('field_id', $this->fieldId),
            ],

            'chapterId' => [
                'required',
                'integer',
                Rule::exists('chapters', 'id')
                    ->where('standard_id', $this->standardId),
            ],

            'type' => [
                'required',
                Rule::enum(QuestionType::class),
            ],

            'text' => ['required', 'string', 'max:10000'],

            'explanation' => ['nullable', 'string', 'max:10000'],

            'difficulty' => [
                'required',
                Rule::enum(QuestionDifficulty::class),
            ],

            'questionImage' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'code' => ['nullable', 'string', 'max:50000'],

            'codeLanguage' => ['nullable', Rule::enum(CodeLanguage::class)],

            'isActive' => ['required', 'boolean'],

            'isFrequentlyFinal' => ['required', 'boolean'],
        ];

        if ($this->type === QuestionType::MultipleChoice->value) {
            $rules['options'] = ['required', 'array', 'size:4'];

            $rules['options.*.text'] = [
                'nullable',
                'string',
                'max:5000',
            ];

            $rules['options.*.image'] = [
                'nullable',
                'image',
                'max:5120',
            ];

            $rules['options.*.dir'] = [
                'required',
                Rule::in(['rtl', 'ltr']),
            ];

            $rules['options.*.is_correct'] = [
                'required',
                'boolean',
            ];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'fieldId.required' => 'رشته آموزشی را انتخاب کنید.',
            'standardId.required' => 'استاندارد را انتخاب کنید.',
            'chapterId.required' => 'فصل را انتخاب کنید.',
            'text.required' => 'متن سؤال الزامی است.',
            'text.max' => 'متن سؤال نباید بیشتر از ۱۰۰۰۰ کاراکتر باشد.',
            'questionImage.image' => 'فایل سؤال باید یک تصویر معتبر باشد.',
            'questionImage.max' => 'حجم تصویر سؤال نباید بیشتر از ۵ مگابایت باشد.',
            'options.size' => 'سؤال چهارگزینه‌ای باید دقیقاً چهار گزینه داشته باشد.',
            'options.*.image.image' => 'فایل هر گزینه باید تصویر معتبر باشد.',
            'options.*.image.max' => 'حجم تصویر هر گزینه نباید بیشتر از ۵ مگابایت باشد.',
        ];
    }

    public function save()
    {
        $this->codeLanguage = $this->codeLanguage ?: null;
        $validated = $this->validate();

        // تضمین می‌کنیم متن سؤال و توضیحات به‌صورت متن ساده ذخیره شوند.
        $questionText = trim(strip_tags($validated['text']));
        $explanation = trim(strip_tags($validated['explanation'] ?? ''));

        if ($questionText === '') {
            $this->addError('text', 'متن سؤال الزامی است.');

            return;
        }

        $validated['text'] = $questionText;

        $validated['explanation'] = $explanation;

        $isMultipleChoice = $validated['type']
            === QuestionType::MultipleChoice->value;

        if ($isMultipleChoice) {
            $correctCount = collect($validated['options'])
                ->filter(fn ($option) => (bool) $option['is_correct'])
                ->count();

            if ($correctCount !== 1) {
                $this->addError(
                    'options',
                    'دقیقاً یک گزینه را به‌عنوان پاسخ صحیح انتخاب کنید.'
                );

                return;
            }

            foreach ($validated['options'] as $index => $option) {
                $optionText = trim(strip_tags($option['text'] ?? ''));
                $hasImage = ! empty($option['image']);

                if ($optionText === '' && ! $hasImage) {
                    $this->addError(
                        "options.{$index}.text",
                        'برای هر گزینه متن یا تصویر وارد کنید.'
                    );
                }
            }

            if ($this->getErrorBag()->isNotEmpty()) {
                return;
            }
        }

        $storedFiles = [];

        try {
            if ($this->questionImage) {
                $path = $this->questionImage->store(
                    'questions',
                    'public'
                );

                if (! $path) {
                    throw new RuntimeException('ذخیره تصویر سؤال ناموفق بود.');
                }

                $storedFiles[] = $path;
            } else {
                $path = null;
            }

            $optionPaths = [];

            if ($isMultipleChoice) {
                foreach ($validated['options'] as $index => $option) {
                    $optionPaths[$index] = null;

                    if (! empty($option['image'])) {
                        $optionPath = $option['image']->store(
                            'question-options',
                            'public'
                        );

                        if (! $optionPath) {
                            throw new RuntimeException(
                                'ذخیره تصویر گزینه ناموفق بود.'
                            );
                        }

                        $storedFiles[] = $optionPath;
                        $optionPaths[$index] = $optionPath;
                    }
                }
            }

            DB::transaction(function () use (
                $validated,
                $isMultipleChoice,
                $path,
                $optionPaths
            ) {
                $question = Question::create([
                    'chapter_id' => $validated['chapterId'],
                    'type' => $validated['type'],
                    'text' => $validated['text'],
                    'explanation' => $validated['explanation'] ?: null,
                    'difficulty' => $validated['difficulty'],
                    'image_path' => $path,
                    'code' => trim($validated['code'] ?? '') ?: null,
                    'code_language' => trim($validated['code'] ?? '') !== ''
                        ? (trim($validated['codeLanguage'] ?? '') ?: null)
                        : null,
                    'is_active' => $validated['isActive'],
                    'is_frequently_final' => $validated['isFrequentlyFinal'],
                    'assigned_by' => auth()->id(),
                ]);

                if (! $isMultipleChoice) {
                    return;
                }

                foreach ($validated['options'] as $index => $option) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => trim(strip_tags($option['text'] ?? '')) ?: null,
                        'image_path' => $optionPaths[$index] ?? null,
                        'dir' => $option['dir'],
                        'is_correct' => (bool) $option['is_correct'],
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach ($storedFiles as $storedFile) {
                Storage::disk('public')->delete($storedFile);
            }

            throw $exception;
        }

        Flux::toast(
            text: 'سؤال با موفقیت ثبت شد.',
            heading: 'عملیات موفق',
            variant: 'success',
            position: 'top right',
        );

        return $this->redirectRoute(
            'question.index',
            array_filter([
                'field_id' => $this->fieldId,
                'standard_id' => $this->standardId,
                'chapter_id' => $this->chapterId,
            ], fn ($value) => $value !== ''),
            navigate: true,
        );
    }
};
