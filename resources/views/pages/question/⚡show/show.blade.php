
<div class="space-y-6">

    {{-- عنوان و مسیر بازگشت --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">نمایش سؤال</flux:heading>
            <flux:text class="mt-1">
                مشاهده جزئیات و گزینه‌های سؤال
            </flux:text>
        </div>

        <flux:button
            :href="route('question.index')"
            variant="ghost"
            icon="arrow-right"
            wire:navigate
        >
            بازگشت به فهرست سؤال‌ها
        </flux:button>
    </div>

    {{-- اطلاعات دسته‌بندی --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">
            اطلاعات دسته‌بندی
        </flux:heading>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <flux:text size="sm" class="text-zinc-500">رشته</flux:text>
                <div class="mt-1 font-medium">
                    {{ $question->chapter->standard->field->title }}
                </div>
            </div>

            <div>
                <flux:text size="sm" class="text-zinc-500">استاندارد</flux:text>
                <div class="mt-1 font-medium">
                    {{ $question->chapter->standard->name_fa }}
                </div>
            </div>

            <div>
                <flux:text size="sm" class="text-zinc-500">فصل</flux:text>
                <div class="mt-1 font-medium">
                    {{ $question->chapter->number }} -
                    {{ $question->chapter->title }}
                </div>
            </div>
        </div>
    </flux:card>

    {{-- مشخصات سؤال --}}
    <flux:card>
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <flux:badge>
                {{ $question->type->label() }}
            </flux:badge>

            <flux:badge>
                {{ $question->difficulty->label() }}
            </flux:badge>

            @if ($question->is_active)
                <flux:badge color="green">فعال</flux:badge>
            @else
                <flux:badge color="red">غیرفعال</flux:badge>
            @endif

            @if ($question->is_frequently_final)
                <flux:badge color="blue">سؤال پرتکرار آزمون نهایی</flux:badge>
            @endif
        </div>

        <flux:heading size="lg" class="mb-3">
            متن سؤال
        </flux:heading>

        <div class="whitespace-pre-wrap leading-8">{{ $question->text }}</div>

        @if ($question->image_path)
            <div class="mt-5">
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($question->image_path) }}"
                    alt="تصویر سؤال"
                    class="max-h-96 max-w-full rounded-lg border"
                >
            </div>
        @endif

        @if ($question->code)
            <div class="mt-6">
                <flux:heading size="md" class="mb-2">
                    کد سؤال
                </flux:heading>

                @if ($question->code_language)
                    <flux:text size="sm" class="mb-2">
                        زبان: {{ $question->code_language }}
                    </flux:text>
                @endif

                <pre dir="ltr" class="overflow-x-auto rounded-lg bg-zinc-100 p-4 text-sm leading-6 dark:bg-zinc-900"><code>{{ $question->code }}</code></pre>
            </div>
        @endif
    </flux:card>

    {{-- گزینه‌ها --}}
    @if ($question->type === \App\Enums\QuestionType::MultipleChoice)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                گزینه‌های سؤال
            </flux:heading>

            <div class="space-y-3">
                @foreach ($question->options as $index => $option)
                    <div
                        wire:key="option-{{ $option->id }}"
                        class="flex items-start gap-3 rounded-lg border p-4
                            {{ $option->is_correct
                                ? 'border-green-500 bg-green-50 dark:bg-green-950/30'
                                : 'border-zinc-200 dark:border-zinc-700' }}"
                    >
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full border font-semibold">
                            {{ ['الف', 'ب', 'ج', 'د'][$index] ?? $index + 1 }}
                        </div>

                        <div class="min-w-0 flex-1">
                            @if ($option->text)
                                <div
                                    class="whitespace-pre-wrap leading-7"
                                    dir="{{ $option->dir }}"
                                >{{ $option->text }}</div>
                            @endif

                            @if ($option->image_path)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($option->image_path) }}"
                                    alt="تصویر گزینه {{ $index + 1 }}"
                                    class="mt-3 max-h-64 max-w-full rounded-lg border"
                                >
                            @endif
                        </div>

                        @if ($option->is_correct)
                            <flux:badge color="green">پاسخ صحیح</flux:badge>
                        @endif
                    </div>
                @endforeach
            </div>
        </flux:card>
    @endif

    {{-- توضیحات پاسخ --}}
    @if ($question->explanation)
        <flux:card>
            <flux:heading size="lg" class="mb-3">
                توضیحات پاسخ
            </flux:heading>

            <div class="whitespace-pre-wrap leading-8">{{ $question->explanation }}</div>
        </flux:card>
    @endif

    {{-- اطلاعات ثبت --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">
            اطلاعات ثبت
        </flux:heading>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <flux:text size="sm" class="text-zinc-500">
                    ثبت‌کننده
                </flux:text>

                <div class="mt-1">
                    {{ $question->assignedBy?->username ?? 'نامشخص' }}
                </div>
            </div>

            <div>
                <flux:text size="sm" class="text-zinc-500">
                    شناسه سؤال
                </flux:text>

                <div class="mt-1">
                    {{ $question->id }}
                </div>
            </div>

            <div>
                <flux:text size="sm" class="text-zinc-500">
                    تاریخ ایجاد
                </flux:text>

                <div class="mt-1">
                    <x-my.jalali_cell :date="$question->jalali_created_at" />
                </div>
            </div>

            <div>
                <flux:text size="sm" class="text-zinc-500">
                    آخرین ویرایش
                </flux:text>

                <div class="mt-1">
                    <x-my.jalali_cell :date="$question->jalali_updated_at" />
                </div>
            </div>
        </div>
    </flux:card>

</div>
