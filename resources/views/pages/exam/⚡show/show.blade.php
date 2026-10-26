<div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6">
    <div class="space-y-2">
        <flux:heading size="xl">
            {{ $exam->title }}
        </flux:heading>

        <flux:text>
            {{ $exam->standard->name_fa ?? $exam->standard->name ?? '' }}
        </flux:text>
    </div>

    @if ($exam->description)
        <flux:card>
            {{ $exam->description }}
        </flux:card>
    @endif

    <flux:card class="space-y-4">
        <div class="flex flex-wrap gap-4">
            <div>
                <flux:text>تعداد سؤال</flux:text>
                <flux:heading>{{ $exam->question_count }}</flux:heading>
            </div>

            <div>
                <flux:text>مدت آزمون</flux:text>
                <flux:heading>
                    {{ intdiv($exam->durationInSeconds(), 60) }} دقیقه
                </flux:heading>
            </div>

            <div>
                <flux:text>حدنصاب قبولی</flux:text>
                <flux:heading>{{ $exam->pass_percentage }}٪</flux:heading>
            </div>
        </div>

        @if ($exam->type === \App\Enums\ExamType::Simulated)
            <flux:separator />

            <flux:heading size="lg">انتخاب خرید آزمون</flux:heading>


            @if ($orderItems->isEmpty())
                <flux:callout variant="warning">
                    <flux:callout.heading>خرید قابل استفاده پیدا نشد</flux:callout.heading>
                    <flux:callout.text>
                        برای شروع این آزمون، باید خرید پرداخت‌شده و استفاده‌نشده داشته باشید.
                    </flux:callout.text>
                </flux:callout>

                @if ($exam->product_id)
                    <form
                        method="POST"
                        action="{{ route('products.buy', ['product' => $exam->product_id]) }}"
                    >
                        @csrf

                        <flux:button
                            type="submit"
                            variant="primary"
                            class="w-full"
                        >
                            خرید و شرکت در آزمون
                        </flux:button>
                    </form>
                @endif
            @else

            <flux:field>
                    <flux:label>خرید موردنظر</flux:label>

                    <flux:select wire:model="selectedOrderItemId">
                        <flux:select.option value="">
                            انتخاب کنید
                        </flux:select.option>

                        @foreach ($orderItems as $orderItem)
                            <flux:select.option value="{{ $orderItem->id }}">
                                سفارش {{ $orderItem->order->order_number ?? $orderItem->order_id }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="selectedOrderItemId" />
                </flux:field>
            @endif
        @endif

        @error('start')
        <flux:callout variant="danger">
            {{ $message }}
        </flux:callout>
        @enderror

        @if ($exam->type !== \App\Enums\ExamType::Simulated || $orderItems->isNotEmpty())
            <flux:button
                variant="primary"
                class="w-full"
                wire:click="startAttempt"
                wire:loading.attr="disabled"
                wire:target="startAttempt"
            >
                <span wire:loading.remove wire:target="startAttempt">
                    شروع آزمون
                </span>

                <span wire:loading wire:target="startAttempt">
                    در حال آماده‌سازی آزمون...
                </span>
            </flux:button>
        @endif
    </flux:card>
</div>
