
<div>
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-2xl
                       bg-blue-100 text-blue-600
                       dark:bg-blue-500/15 dark:text-blue-400"
            >
                <flux:icon.pencil-square variant="outline" class="size-7" />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    ویرایش سؤال
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    ویرایش محتوای سؤال، گزینه‌ها و تنظیمات
                </flux:text>
            </div>
        </div>

        <div class="mt-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item
                    href="{{ route('question.index', array_filter([
                        'field_id' => $fieldId,
                        'standard_id' => $standardId,
                        'chapter_id' => $chapterId,
                    ], fn ($value) => $value !== '')) }}"
                    wire:navigate
                >
                    بانک سؤال‌ها
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item
                    href="{{ route('question.show', $question) }}"
                    wire:navigate
                >
                    نمایش سؤال
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    ویرایش سؤال
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- محل قرارگیری سؤال --}}
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">
                محل قرارگیری سؤال
            </flux:heading>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <flux:field>
                    <flux:label>رشته آموزشی</flux:label>

                    <flux:select wire:model.live="fieldId">
                        <flux:select.option value="">
                            انتخاب رشته
                        </flux:select.option>

                        @foreach ($this->fields as $field)
                            <flux:select.option value="{{ $field->id }}">
                                {{ $field->title }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="fieldId" />
                </flux:field>

                <flux:field>
                    <flux:label>استاندارد</flux:label>

                    <flux:select wire:model.live="standardId">
                        <flux:select.option value="">
                            انتخاب استاندارد
                        </flux:select.option>

                        @foreach ($this->standards as $standard)
                            <flux:select.option value="{{ $standard->id }}">
                                {{ $standard->code }} - {{ $standard->name_fa }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="standardId" />
                </flux:field>

                <flux:field>
                    <flux:label>فصل</flux:label>

                    <flux:select wire:model="chapterId">
                        <flux:select.option value="">
                            انتخاب فصل
                        </flux:select.option>

                        @foreach ($this->chapters as $chapter)
                            <flux:select.option value="{{ $chapter->id }}">
                                فصل {{ $chapter->number }} - {{ $chapter->title }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="chapterId" />
                </flux:field>
            </div>
        </div>

        {{-- محتوای سؤال --}}
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">
                محتوای سؤال
            </flux:heading>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>نوع سؤال</flux:label>

                    <flux:select wire:model.live="type">
                        @foreach (\App\Enums\QuestionType::cases() as $questionType)
                            <flux:select.option value="{{ $questionType->value }}">
                                {{ $questionType->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="type" />
                </flux:field>

                <flux:field>
                    <flux:label>سطح دشواری</flux:label>

                    <flux:select wire:model="difficulty">
                        @foreach (\App\Enums\QuestionDifficulty::cases() as $difficulty)
                            <flux:select.option value="{{ $difficulty->value }}">
                                {{ $difficulty->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="difficulty" />
                </flux:field>
            </div>

            <flux:field class="mt-4">
                <flux:label>متن سؤال</flux:label>

                <flux:textarea
                    wire:model="text"
                    rows="5"
                    placeholder="متن سؤال را وارد کنید..."
                />

                <flux:error name="text" />
            </flux:field>

            {{-- تصویر فعلی و تصویر جدید سؤال --}}
            <div class="mt-4">
                <flux:field>
                    <flux:label>تصویر سؤال (اختیاری)</flux:label>

                    @if ($questionImage)
                        <div class="mb-3">
                            <flux:text class="mb-2 text-sm">
                                پیش‌نمایش تصویر جدید
                            </flux:text>

                            <img
                                src="{{ $questionImage->temporaryUrl() }}"
                                alt="پیش‌نمایش تصویر جدید سؤال"
                                class="max-h-64 max-w-full rounded-lg border border-zinc-200 object-contain dark:border-zinc-700"
                            />
                        </div>
                    @elseif ($question->image_path)
                        <div class="mb-3">
                            <flux:text class="mb-2 text-sm">
                                تصویر فعلی سؤال
                            </flux:text>

                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($question->image_path) }}"
                                alt="تصویر فعلی سؤال"
                                class="max-h-64 max-w-full rounded-lg border border-zinc-200 object-contain dark:border-zinc-700"
                            />
                        </div>
                    @endif

                    <input
                        type="file"
                        wire:model="questionImage"
                        accept="image/*"
                        class="block w-full text-sm text-zinc-600
                               file:me-3 file:rounded-lg file:border-0
                               file:bg-zinc-100 file:px-4 file:py-2
                               file:text-sm file:font-medium
                               dark:text-zinc-300 dark:file:bg-zinc-800"
                    />

                    <flux:error name="questionImage" />

                    <div
                        wire:loading
                        wire:target="questionImage"
                        class="mt-2 text-sm text-blue-600"
                    >
                        در حال بارگذاری تصویر...
                    </div>

                    <flux:text class="mt-2 text-xs text-zinc-500">
                        اگر تصویر جدیدی انتخاب نکنید، تصویر فعلی حفظ می‌شود.
                    </flux:text>
                </flux:field>
            </div>

            <div class="mt-4">
                <flux:field>
                    <flux:label>کد برنامه‌نویسی (اختیاری)</flux:label>

                    <flux:textarea
                        wire:model="code"
                        rows="6"
                        dir="ltr"
                        placeholder="کد را در این قسمت وارد کنید..."
                        class="font-mono"
                    />

                    <flux:error name="code" />
                </flux:field>
            </div>

            <div class="mt-4">
                <flux:select wire:model="codeLanguage" label="زبان برنامه‌نویسی">
                    <flux:select.option value="">بدون زبان مشخص</flux:select.option>

                    @foreach ($this->codeLanguages as $language)
                        <flux:select.option value="{{ $language->value }}">
                            {{ $language->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:field class="mt-4">
                <flux:label>توضیح پاسخ (اختیاری)</flux:label>

                <flux:textarea
                    wire:model="explanation"
                    rows="4"
                    placeholder="دلیل صحیح بودن پاسخ یا توضیحات تکمیلی..."
                />

                <flux:error name="explanation" />
            </flux:field>
        </div>

        {{-- گزینه‌ها --}}
        @if ($type === \App\Enums\QuestionType::MultipleChoice->value)
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:heading size="lg">
                    گزینه‌های سؤال
                </flux:heading>

                <flux:text class="mt-1 mb-4 text-sm text-zinc-500 dark:text-zinc-400">
                    سؤال باید دقیقاً چهار گزینه و یک پاسخ صحیح داشته باشد.
                </flux:text>

                <flux:error name="options" />

                <div class="space-y-5">
                    @foreach ($options as $index => $option)
                        @php
                            $existingOption = ! empty($option['id'])
                                ? $question->options->firstWhere('id', $option['id'])
                                : null;
                        @endphp

                        <div
                            wire:key="question-option-{{ $option['id'] ?? 'new-'.$index }}"
                            class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700"
                        >
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <flux:heading size="md">
                                    گزینه {{ $index + 1 }}
                                </flux:heading>

                                <label class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        wire:model="options.{{ $index }}.is_correct"
                                        class="size-4 rounded border-zinc-300"
                                    />

                                    پاسخ صحیح
                                </label>
                            </div>

                            <flux:field>
                                <flux:label>متن گزینه</flux:label>

                                <flux:textarea
                                    wire:model="options.{{ $index }}.text"
                                    rows="2"
                                    placeholder="متن گزینه را وارد کنید..."
                                />

                                <flux:error name="options.{{ $index }}.text" />
                            </flux:field>

                            <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <flux:field>
                                    <flux:label>تصویر گزینه (اختیاری)</flux:label>

                                    @if (! empty($option['image']))
                                        <img
                                            src="{{ $option['image']->temporaryUrl() }}"
                                            alt="پیش‌نمایش تصویر جدید گزینه"
                                            class="mb-3 max-h-48 max-w-full rounded-lg border border-zinc-200 object-contain dark:border-zinc-700"
                                        />
                                    @elseif ($existingOption?->image_path)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingOption->image_path) }}"
                                            alt="تصویر فعلی گزینه"
                                            class="mb-3 max-h-48 max-w-full rounded-lg border border-zinc-200 object-contain dark:border-zinc-700"
                                        />
                                    @endif

                                    <input
                                        type="file"
                                        wire:model="options.{{ $index }}.image"
                                        accept="image/*"
                                        class="block w-full text-sm text-zinc-600
                                               file:me-3 file:rounded-lg file:border-0
                                               file:bg-zinc-100 file:px-3 file:py-2
                                               dark:text-zinc-300 dark:file:bg-zinc-800"
                                    />

                                    <flux:error name="options.{{ $index }}.image" />

                                    <flux:text class="mt-2 text-xs text-zinc-500">
                                        انتخاب تصویر جدید، تصویر فعلی این گزینه را جایگزین می‌کند.
                                    </flux:text>
                                </flux:field>

                                <flux:field>
                                    <flux:label>جهت متن گزینه</flux:label>

                                    <flux:select wire:model="options.{{ $index }}.dir">
                                        <flux:select.option value="rtl">
                                            راست به چپ
                                        </flux:select.option>

                                        <flux:select.option value="ltr">
                                            چپ به راست
                                        </flux:select.option>
                                    </flux:select>

                                    <flux:error name="options.{{ $index }}.dir" />
                                </flux:field>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- تنظیمات سؤال --}}
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">
                تنظیمات سؤال
            </flux:heading>

            <div class="space-y-4">
                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model="isFrequentlyFinal"
                        class="size-4 rounded border-zinc-300"
                    />

                    <div>
                        <div class="text-sm font-medium">
                            سؤال پرتکرار آزمون نهایی
                        </div>

                        <div class="text-xs text-zinc-500">
                            این سؤال در مجموعه نمونه‌سؤال‌های پرتکرار قرار بگیرد.
                        </div>
                    </div>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model="isActive"
                        class="size-4 rounded border-zinc-300"
                    />

                    <div>
                        <div class="text-sm font-medium">
                            سؤال فعال باشد
                        </div>

                        <div class="text-xs text-zinc-500">
                            سؤال غیرفعال در بانک باقی می‌ماند، اما بعداً از نمایش عمومی آن جلوگیری می‌کنیم.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- دکمه‌ها --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <flux:button
                variant="ghost"
                :href="route('question.show', $question)"
                wire:navigate
            >
                انصراف
            </flux:button>

            <flux:button
                type="submit"
                variant="primary"
                icon="check"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">
                    ذخیره تغییرات
                </span>

                <span wire:loading wire:target="save">
                    در حال ذخیره...
                </span>
            </flux:button>
        </div>
    </form>
</div>
