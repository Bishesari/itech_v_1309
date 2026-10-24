<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <div>
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
            آزمون‌ها
        </h1>

        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            آزمون موردنظر خود را انتخاب کنید.
        </p>
    </div>

    @if ($exams->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
            <flux:icon name="clipboard-document-list" class="mx-auto size-10 text-zinc-400" />

            <p class="mt-3 font-medium text-zinc-700 dark:text-zinc-200">
                در حال حاضر آزمون قابل دسترسی وجود ندارد.
            </p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($exams as $exam)
                <article
                    wire:key="exam-{{ $exam->id }}"
                    class="flex flex-col rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900"
                >
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-bold text-zinc-900 dark:text-white">
                            {{ $exam->title }}
                        </h2>

                        <span class="shrink-0 rounded-full bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-800 dark:bg-sky-900 dark:text-sky-200">
                            {{ $exam->type->label() }}
                        </span>
                    </div>

                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                        استاندارد:
                        {{ $exam->standard->name_fa ?? '—' }}
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                            <p class="text-zinc-500 dark:text-zinc-400">
                                تعداد سؤال
                            </p>

                            <p class="mt-1 font-semibold text-zinc-900 dark:text-white">
                                {{ $exam->question_count }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                            <p class="text-zinc-500 dark:text-zinc-400">
                                مدت آزمون
                            </p>

                            <p class="mt-1 font-semibold text-zinc-900 dark:text-white">
                                {{ intdiv($exam->durationInSeconds(), 60) }} دقیقه
                            </p>
                        </div>
                    </div>

                    @if ($exam->description)
                        <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                            {{ $exam->description }}
                        </p>
                    @endif

                    <div class="mt-auto pt-5">
                        <flux:button
                            :href="route('exam.show', $exam)"
                            variant="outline"
                            class="w-full"
                        >
                            مشاهده جزئیات
                            <flux:icon name="arrow-left" class="size-4" />
                        </flux:button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
