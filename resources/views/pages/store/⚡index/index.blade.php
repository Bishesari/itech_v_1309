<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <flux:heading size="xl" level="1">
            فروشگاه
        </flux:heading>

        <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
            محصولات و خدمات قابل عرضه را مشاهده کنید.
        </flux:text>
    </div>

    <flux:separator variant="subtle" />

    @if ($this->products->isEmpty())
        <div class="py-16 text-center">
            <flux:icon.shopping-bag
                variant="outline"
                class="mx-auto size-12 text-zinc-400"
            />

            <flux:heading size="lg" class="mt-4">
                محصولی برای نمایش وجود ندارد.
            </flux:heading>

            <flux:text class="mt-2 text-zinc-500 dark:text-zinc-400">
                در حال حاضر محصولی در فروشگاه عرضه نشده است.
            </flux:text>
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->products as $product)
                @php
                    $price = $product->currentPrice();
                @endphp

                <article
                    wire:key="store-product-{{ $product->id }}"
                    class="flex flex-col rounded-2xl border border-zinc-200
                           bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex size-11 shrink-0 items-center justify-center
                                   rounded-xl bg-blue-100 text-blue-600
                                   dark:bg-blue-500/15 dark:text-blue-400"
                        >
                            <flux:icon.shopping-bag
                                variant="outline"
                                class="size-6"
                            />
                        </div>

                        <div class="min-w-0 flex-1">
                            <flux:heading size="lg">
                                {{ $product->title }}
                            </flux:heading>
                        </div>
                    </div>

                    @if ($product->short_description)
                        <p class="mt-4 text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                            {{ $product->short_description }}
                        </p>
                    @endif

                    <div class="mt-auto pt-6">
                        <div class="mb-4">
                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                                قیمت
                            </flux:text>

                            @if ($price)
                                <div class="mt-1 font-semibold">
                                    {{ number_format($price->amount) }}
                                    <span class="text-sm font-normal text-zinc-500">
                                        ریال
                                    </span>
                                </div>
                            @else
                                <div class="mt-1 text-sm text-zinc-500">
                                    قیمت اعلام نشده است.
                                </div>
                            @endif
                        </div>

                        @if ($product->type->value === 'written_sample_question')
                            <flux:button
                                href="{{ route('store.written-sample-questions.show', ['product' => $product->slug]) }}"
                                wire:navigate
                                variant="outline"
                                color="blue"
                                class="w-full"
                            >
                                مشاهده جزئیات
                            </flux:button>
                        @else
                            <flux:button
                                variant="outline"
                                color="zinc"
                                class="w-full"
                                disabled
                            >
                                جزئیات به‌زودی
                            </flux:button>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $this->products->links() }}
        </div>
    @endif
</div>
