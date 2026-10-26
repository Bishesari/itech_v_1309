<div
    class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6"
    x-data="{
        remaining: {{ $remainingSeconds }},
        timer: null,
        init() {
            this.timer = setInterval(() => {
                if (this.remaining > 0) {
                    this.remaining--;
                } else {
                    clearInterval(this.timer);
                    $wire.finish();
                }
            }, 1000);
        },
        destroy() {
            clearInterval(this.timer);
        },
        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
    }"
>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="space-y-1">
            <flux:heading size="xl">{{ $attempt->exam->title }}</flux:heading>
            <flux:text>
                سؤال {{ $currentPosition }} از {{ $attempt->question_count }}
            </flux:text>
        </div>

        <div class="rounded-xl border px-4 py-3 text-center">
            <flux:text size="sm">زمان باقی‌مانده</flux:text>
            <div class="font-mono text-xl font-semibold" x-text="formatTime(remaining)"></div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach ($attempt->questions as $item)
            <flux:button
                size="sm"
                variant="{{ $item->position === $currentPosition ? 'primary' : ($item->selected_option_key ? 'filled' : 'ghost') }}"
                wire:click="goToQuestion({{ $item->position }})"
            >
                {{ $item->position }}
            </flux:button>
        @endforeach
    </div>

    @if ($questionData)
        <div class="space-y-5 rounded-2xl border p-5 sm:p-7">
            <div class="text-base leading-8">
                {{ $questionData['text'] ?? '' }}
            </div>

            @if (! empty($questionData['image_path']))
                <img
                    src="{{ asset('storage/' . $questionData['image_path']) }}"
                    alt=""
                    class="max-h-96 max-w-full rounded-xl object-contain"
                >
            @endif

            @if (! empty($questionData['code']))
                <pre class="overflow-x-auto rounded-xl bg-zinc-100 p-4 text-left text-sm dark:bg-zinc-900"><code>{{ $questionData['code'] }}</code></pre>
            @endif

            <div class="space-y-3">
                @foreach ($options as $option)
                    <label
                        wire:key="option-{{ $attemptQuestion->id }}-{{ $option['key'] }}"
                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition hover:bg-zinc-50 dark:hover:bg-zinc-800"
                    >
                        <input
                            type="radio"
                            name="answer-{{ $attemptQuestion->id }}"
                            value="{{ $option['key'] }}"
                            wire:model="selectedOptionKey"
                            wire:change="saveAnswer($event.target.value)"
                            class="mt-1"
                        >

                        <div class="min-w-0 flex-1">
                            <div dir="{{ $option['dir'] }}">
                                {{ $option['text'] }}
                            </div>

                            @if (! empty($option['image_path']))
                                <img
                                    src="{{ asset('storage/' . $option['image_path']) }}"
                                    alt=""
                                    class="mt-3 max-h-48 rounded-lg object-contain"
                                >
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    @else
        <flux:callout variant="danger">
            اطلاعات این سؤال در دسترس نیست.
        </flux:callout>
    @endif

    <div class="flex flex-wrap justify-between gap-3">
        <flux:button
            variant="ghost"
            wire:click="goToQuestion({{ max(1, $currentPosition - 1) }})"
            :disabled="$currentPosition <= 1"
        >
            سؤال قبلی
        </flux:button>

        @if ($currentPosition < $attempt->question_count)
            <flux:button
                variant="primary"
                wire:click="goToQuestion({{ $currentPosition + 1 }})"
            >
                سؤال بعدی
            </flux:button>
        @else
            <flux:button
                variant="danger"
                wire:click="finish"
                wire:confirm="آیا از ثبت نهایی آزمون مطمئن هستید؟"
            >
                ثبت نهایی آزمون
            </flux:button>
        @endif
    </div>
</div>
