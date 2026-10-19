<?php

use App\Enums\CodeLanguage;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Chapter;
use App\Models\Field;
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

new #[Title('ویرایش سؤال')] class extends Component
{
    use WithFileUploads;

    public Question $question;

    #[Url(as: 'field_id')]
    public string $fieldId = '';

    #[Url(as: 'standard_id')]
    public string $standardId = '';

    #[Url(as: 'chapter_id')]
    public string $chapterId = '';

    public string $type = 'multiple_choice';

    public string $text = '';

    #[Computed]
    public function codeLanguages(): array
    {
        return CodeLanguage::cases();
    }

    public bool $removeQuestionImage = false;
    public string $explanation = '';

    public string $difficulty = 'medium';

    public $questionImage = null;

    public string $code = '';

    public ?string $codeLanguage = null;

    public bool $isActive = true;

    public bool $isFrequentlyFinal = false;

    public array $options = [];

    public function mount(Question $question): void
    {
        $this->question = $question->load([
            'chapter.standard.field',
            'options',
            'assignedBy',
        ]);

        $this->fieldId = (string) $question->chapter->standard->field_id;
        $this->standardId = (string) $question->chapter->standard_id;
        $this->chapterId = (string) $question->chapter_id;

        $this->type = $question->type->value;
        $this->text = $question->text;
        $this->explanation = $question->explanation ?? '';
        $this->difficulty = $question->difficulty->value;
        $this->code = $question->code ?? '';
        $this->codeLanguage = $question->code_language ?? '';
        $this->isActive = (bool) $question->is_active;
        $this->isFrequentlyFinal = (bool) $question->is_frequently_final;

        foreach ($question->options as $option) {
            $this->options[] = [
                'id' => $option->id,
                'text' => $option->text ?? '',
                'image' => null,
                'dir' => $option->dir,
                'is_correct' => (bool) $option->is_correct,
            ];
        }

        // در صورت نبودن چهار گزینه در داده‌های قبلی،
        // ورودی‌های خالی را برای تکمیل فرم اضافه می‌کنیم.
        while (count($this->options) < 4) {
            $this->options[] = [
                'id' => null,
                'text' => '',
                'image' => null,
                'dir' => 'rtl',
                'is_correct' => false,
            ];
        }

        $this->options = array_slice($this->options, 0, 4);
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
            'fieldId' => [
                'required',
                'integer',
                'exists:fields,id',
            ],

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

            'text' => [
                'required',
                'string',
                'max:10000',
            ],

            'explanation' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'difficulty' => [
                'required',
                Rule::enum(QuestionDifficulty::class),
            ],

            'questionImage' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50000',
            ],

            'codeLanguage' => ['nullable', Rule::enum(CodeLanguage::class)],

            'isActive' => [
                'required',
                'boolean',
            ],

            'isFrequentlyFinal' => [
                'required',
                'boolean',
            ],
        ];

        if ($this->type === QuestionType::MultipleChoice->value) {
            $rules['options'] = [
                'required',
                'array',
                'size:4',
            ];

            $rules['options.*.id'] = [
                'nullable',
                'integer',
                Rule::exists('options', 'id')
                    ->where('question_id', $this->question->id),
            ];

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

        $existingOptions = $this->question->options()
            ->get()
            ->keyBy('id');

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

                $existingOption = ! empty($option['id'])
                    ? $existingOptions->get($option['id'])
                    : null;

                $hasExistingImage = $existingOption
                    && $existingOption->image_path;

                $hasNewImage = ! empty($option['image']);

                if (
                    $optionText === ''
                    && ! $hasExistingImage
                    && ! $hasNewImage
                ) {
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

        // مسیرهای فایل‌های قبلی را نگه می‌داریم تا پس از موفقیت
        // ذخیره‌سازی، فقط فایل‌های واقعاً جایگزین‌شده حذف شوند.
        $oldPaths = array_filter([
            $this->question->image_path,
            ...$existingOptions->pluck('image_path')->all(),
        ]);

        try {
            $questionImagePath = $this->question->image_path;

            if ($this->questionImage) {
                $questionImagePath = $this->questionImage->store(
                    'questions',
                    'public'
                );

                if (! $questionImagePath) {
                    throw new RuntimeException(
                        'ذخیره تصویر سؤال ناموفق بود.'
                    );
                }

                $storedFiles[] = $questionImagePath;
            }

            $optionPaths = [];

            if ($isMultipleChoice) {
                foreach ($validated['options'] as $index => $option) {
                    $existingOption = ! empty($option['id'])
                        ? $existingOptions->get($option['id'])
                        : null;

                    $optionPath = $existingOption?->image_path;

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
                    }

                    $optionPaths[$index] = $optionPath;
                }
            }

            DB::transaction(function () use (
                $validated,
                $isMultipleChoice,
                $questionImagePath,
                $optionPaths,
                $existingOptions
            ) {
                $this->question->update([
                    'chapter_id' => $validated['chapterId'],
                    'type' => $validated['type'],
                    'text' => $validated['text'],
                    'explanation' => $validated['explanation'] ?: null,
                    'difficulty' => $validated['difficulty'],
                    'image_path' => $questionImagePath,
                    'code' => trim($validated['code'] ?? '') ?: null,
                    'code_language' => trim($validated['code'] ?? '') !== ''
                        ? (trim($validated['codeLanguage'] ?? '') ?: null)
                        : null,
                    'is_active' => $validated['isActive'],
                    'is_frequently_final' => $validated['isFrequentlyFinal'],
                ]);

                if (! $isMultipleChoice) {
                    $this->question->options()->delete();

                    return;
                }

                $keptOptionIds = [];

                foreach ($validated['options'] as $index => $option) {
                    $data = [
                        'text' => trim(strip_tags($option['text'] ?? '')) ?: null,
                        'image_path' => $optionPaths[$index] ?? null,
                        'dir' => $option['dir'],
                        'is_correct' => (bool) $option['is_correct'],
                    ];

                    if (! empty($option['id'])) {
                        $existingOption = $existingOptions->get($option['id']);

                        $existingOption->update($data);

                        $keptOptionIds[] = $existingOption->id;
                    } else {
                        $newOption = $this->question->options()->create($data);

                        $keptOptionIds[] = $newOption->id;
                    }
                }

                $optionsQuery = $this->question->options();

                if ($keptOptionIds !== []) {
                    $optionsQuery->whereNotIn('id', $keptOptionIds);
                }

                $optionsQuery->delete();
            });
        } catch (Throwable $exception) {
            foreach ($storedFiles as $storedFile) {
                Storage::disk('public')->delete($storedFile);
            }

            throw $exception;
        }

        // پس از موفقیت تراکنش، فایل‌هایی که دیگر به سؤال یا
        // گزینه‌های آن متصل نیستند حذف می‌شوند.
        $currentPaths = array_filter([
            $this->question->fresh()->image_path,
            ...$this->question->options()->pluck('image_path')->all(),
        ]);

        foreach (array_unique($oldPaths) as $oldPath) {
            if (! in_array($oldPath, $currentPaths, true)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        Flux::toast(
            text: 'تغییرات سؤال با موفقیت ذخیره شد.',
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

    public function removeQuestionImage(): void
    {
        $this->questionImage = null;
        $this->removeQuestionImage = true;

        $this->resetValidation('questionImage');
    }

    public function removeOptionImage(int $index): void
    {
        if (! isset($this->options[$index])) {
            return;
        }

        $this->options[$index]['image'] = null;
        $this->options[$index]['remove_image'] = true;

        $this->resetValidation("options.{$index}.image");
    }
};
