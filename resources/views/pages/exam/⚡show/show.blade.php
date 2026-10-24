<div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6">
    <div>
        <a
            href="{{ route('exam.index') }}"
            class="inline-flex items-center gap-2 text-sm text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
        >
            <flux:icon name="arrow-right" class="size-4" />
            بازگشت به فهرست آزمون‌ها
        </a>
    </div>

    <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-8 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $exam->type->label() }}
                </p>

                <h1 class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ $exam->title }}
                </h1>

                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                    استاندارد:
                    {{ $exam->standard->name_fa ?? '—' }}
                </p>
            </div>

            <span class="rounded-full bg-sky-100 px-3 py-1.5 text-sm font-medium text-sky-800 dark:bg-sky-900 dark:text-sky-200">
                {{ $exam->question_count }} سؤال
            </span>
        </div>

        @if ($exam->description)
            <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <h2 class="font-semibold text-zinc-900 dark:text-white">
                    درباره آزمون
                </h2>

                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                    {{ $exam->description }}
                </p>
            </div>
        @endif

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800">
                <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="clock" class="size-5" />
                    مدت آزمون
                </div>

                <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ intdiv($exam->durationInSeconds(), 60) }} دقیقه
                </p>
            </div>

            <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800">
                <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="check-circle" class="size-5" />
                    حد نصاب قبولی
                </div>

                <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $exam->pass_percentage !== null ? $exam->pass_percentage . '٪' : 'تعریف نشده' }}
                </p>
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
            پس از شروع آزمون، زمان آن محاسبه می‌شود. شرایط و امکان شروع آزمون در مرحله بعد بررسی خواهد شد.
        </div>

        <div class="mt-6">
            <flux:button
                href="{{ route('exam.index') }}"
                variant="ghost"
                class="w-full sm:w-auto"
            >
                بازگشت
            </flux:button>
        </div>
    </section>
</div>
