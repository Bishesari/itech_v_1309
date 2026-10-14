<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <nav
        aria-label="مسیر صفحه"
        class="mb-8 text-sm text-zinc-500 dark:text-zinc-400"
    >
        <ol class="flex flex-wrap items-center gap-2">
            <li>
                <a
                    href="{{ url('/') }}"
                    class="transition hover:text-zinc-900 dark:hover:text-white"
                >
                    خانه
                </a>
            </li>

            <li aria-hidden="true">/</li>

            <li>
                <a
                    href="{{ url('/store') }}"
                    class="transition hover:text-zinc-900 dark:hover:text-white"
                >
                    فروشگاه
                </a>
            </li>

            <li aria-hidden="true">/</li>

            <li class="text-zinc-900 dark:text-white">
                {{ $product->title }}
            </li>
        </ol>
    </nav>

    {{-- Product introduction and purchase panel --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- Product information --}}
        <section class="space-y-6 lg:col-span-2">

            <div
                class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 sm:p-8"
            >
                <div class="mb-5 flex flex-wrap items-center gap-2">

                    <span
                        class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700 dark:bg-sky-950 dark:text-sky-300"
                    >
                        نمونه سؤالات پرتکرار
                    </span>

                </div>

                <h1
                    class="text-2xl font-bold leading-relaxed text-zinc-900 dark:text-white sm:text-3xl"
                >
                    {{ $product->title }}
                </h1>

                @if ($product->short_description)
                    <p
                        class="mt-5 text-base leading-8 text-zinc-600 dark:text-zinc-300"
                    >
                        {{ $product->short_description }}
                    </p>
                @endif
            </div>

            {{-- Full description --}}
            @if ($product->description)
                <section
                    class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 sm:p-8"
                >
                    <h2
                        class="mb-5 text-xl font-bold text-zinc-900 dark:text-white"
                    >
                        درباره این محصول
                    </h2>

                    <div
                        class="leading-9 text-zinc-600 dark:text-zinc-300"
                    >
                        {{ $product->description }}
                    </div>
                </section>
            @endif

        </section>

        {{-- Purchase panel --}}
        <aside class="lg:col-span-1">
            <div
                class="sticky top-6 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900"
            >
                <h2
                    class="text-lg font-bold text-zinc-900 dark:text-white"
                >
                    خرید محصول
                </h2>

                @php
                    $price = $product->currentPrice();
                @endphp

                <div class="mt-6 border-b border-zinc-200 pb-6 dark:border-zinc-700">

                    @if ($price)
                        <div class="flex items-baseline gap-2">
                            <span
                                class="text-3xl font-bold text-zinc-900 dark:text-white"
                            >
                                {{ number_format($price->amount) }}
                            </span>

                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                ریال
                            </span>
                        </div>
                    @else
                        <p class="font-medium text-amber-600 dark:text-amber-400">
                            قیمت این محصول در حال حاضر مشخص نشده است.
                        </p>
                    @endif

                </div>

                <div class="mt-6 space-y-4">

                    <div class="flex items-start gap-3 text-sm text-zinc-600 dark:text-zinc-300">
{{--                        <x-heroicon-o-document-text--}}
{{--                            class="mt-0.5 size-5 shrink-0"--}}
{{--                        />--}}
                        <span>
                            مشاهده مشخصات و توضیحات محصول پیش از خرید
                        </span>
                    </div>

                    <div class="flex items-start gap-3 text-sm text-zinc-600 dark:text-zinc-300">
{{--                        <x-heroicon-o-shield-check--}}
{{--                            class="mt-0.5 size-5 shrink-0"--}}
{{--                        />--}}
                        <span>
                            دسترسی به محصول پس از تأیید خرید
                        </span>
                    </div>

                </div>

                @if ($receiptOrderNumber !== null)
                    <section
                        class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-950/30"
                        aria-labelledby="payment-receipt-title"
                    >
                        <div class="flex items-center gap-2">
                            <flux:icon.check-circle class="size-6 text-emerald-600" />

                            <h2
                                id="payment-receipt-title"
                                class="text-lg font-semibold text-emerald-800 dark:text-emerald-300"
                            >
                                پرداخت با موفقیت انجام شد
                            </h2>
                        </div>

                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                            سفارش شما ثبت شده است. اطلاعات رسید پرداخت:
                        </p>

                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex flex-wrap justify-between gap-2">
                                <dt class="text-zinc-500">شماره سفارش</dt>
                                <dd class="font-medium" dir="ltr">
                                    {{ $receiptOrderNumber }}
                                </dd>
                            </div>

                            <div class="flex flex-wrap justify-between gap-2">
                                <dt class="text-zinc-500">مبلغ پرداختی</dt>
                                <dd class="font-semibold">
                                    {{ number_format($receiptAmount) }} ریال
                                </dd>
                            </div>

                            @if ($receiptReference)
                                <div class="flex flex-wrap justify-between gap-2">
                                    <dt class="text-zinc-500">شماره پیگیری درگاه</dt>
                                    <dd class="font-normal text-xs" dir="ltr">
                                        {{ $receiptReference }}
                                    </dd>
                                </div>
                            @endif

                            @if ($receiptPaidAt)
                                <div class="flex flex-wrap justify-between gap-2">
                                    <dt class="text-zinc-500">زمان پرداخت</dt>
                                    <dd class="font-medium" dir="ltr">
                                        {{ $receiptPaidAt }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </section>
                @endif

                @if ($price && $price->amount > 0)
                    @auth
                        @if ($receiptOrderNumber !== null)
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a
                                    href="#product-content"
                                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-700"
                                >
                                    مشاهده محصول
                                </a>
                            </div>
                        @else
                            <form
                                method="POST"
                                action="{{ route('products.buy', ['product' => $product->getKey()]) }}"
                                class="mt-8"
                                x-data="{ submitting: false }"
                                @submit="if (submitting) { $event.preventDefault(); return; } submitting = true"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                                >
                                    خرید محصول
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login', ['redirect' => route('store.written-sample-questions.show', ['product' => $product->slug]),]) }}"
                           class="mt-8 block w-full rounded-xl bg-sky-600 px-5 py-3 text-center font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                        >
                            ورود برای خرید
                        </a>
                    @endauth
                @else
                    <button
                        type="button"
                        disabled
                        class="mt-8 w-full cursor-not-allowed rounded-xl bg-zinc-200 px-5 py-3 font-semibold text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400"
                    >
                        خرید محصول فعلاً امکان‌پذیر نیست
                    </button>
                @endif

                @error('product')
                <p class="mt-3 text-sm leading-6 text-red-600 dark:text-red-400">
                    {{ $message }}
                </p>
                @enderror

                @error('order')
                <p class="mt-3 text-sm leading-6 text-red-600 dark:text-red-400">
                    {{ $message }}
                </p>
                @enderror

                @error('payment')
                <p class="mt-3 text-sm leading-6 text-red-600 dark:text-red-400">
                    {{ $message }}
                </p>
                @enderror

                <p class="mt-3 text-center text-xs leading-6 text-zinc-500 dark:text-zinc-400">
                    پس از ثبت سفارش، برای پرداخت آنلاین به درگاه بانکی منتقل خواهید شد.
                </p>
            </div>
        </aside>

    </div>

</div>
