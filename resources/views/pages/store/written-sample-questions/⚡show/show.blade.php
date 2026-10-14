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

    {{-- Product information and purchase panel --}}
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
                    وضعیت محصول
                </h2>

                @php
                    $price = $product->currentPrice();
                @endphp

                {{-- Price --}}
                <div
                    class="mt-6 border-b border-zinc-200 pb-6 dark:border-zinc-700"
                >
                    @if ($this->hasAccess)
                        <p class="font-semibold text-emerald-700 dark:text-emerald-400">
                            این محصول در اختیار شماست.
                        </p>
                    @elseif ($price)
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

                {{-- Product features --}}
                <div class="mt-6 space-y-4">
                    <div class="flex items-start gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <span>
                            مشاهده مشخصات و توضیحات محصول
                        </span>
                    </div>

                    <div class="flex items-start gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <span>
                            دسترسی به محتوای محصول پس از تأیید خرید
                        </span>
                    </div>
                </div>

                {{-- Payment receipt --}}
                @if ($receiptOrderNumber !== null)
                    <section
                        class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-950/30"
                        aria-labelledby="payment-receipt-title"
                    >
                        <div class="flex items-center gap-2">
                            <flux:icon.check-circle
                                class="size-6 text-emerald-600"
                            />

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
                                <dt class="text-zinc-500">
                                    شماره سفارش
                                </dt>

                                <dd class="font-medium" dir="ltr">
                                    {{ $receiptOrderNumber }}
                                </dd>
                            </div>

                            <div class="flex flex-wrap justify-between gap-2">
                                <dt class="text-zinc-500">
                                    مبلغ پرداختی
                                </dt>

                                <dd class="font-semibold">
                                    {{ number_format($receiptAmount ?? 0) }} ریال
                                </dd>
                            </div>

                            @if ($receiptReference)
                                <div class="flex flex-wrap justify-between gap-2">
                                    <dt class="text-zinc-500">
                                        شماره پیگیری درگاه
                                    </dt>

                                    <dd class="text-xs font-normal" dir="ltr">
                                        {{ $receiptReference }}
                                    </dd>
                                </div>
                            @endif

                            @if ($receiptPaidAt)
                                <div class="flex flex-wrap justify-between gap-2">
                                    <dt class="text-zinc-500">
                                        زمان پرداخت
                                    </dt>

                                    <dd class="font-medium" dir="ltr">
                                        {{ $receiptPaidAt }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </section>
                @endif

                {{-- Purchase / access button --}}
                @if ($this->hasAccess)
                    <div class="mt-8">
                        <a
                            href="#product-content"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-700"
                        >
                            مشاهده محتوای محصول
                        </a>
                    </div>
                @elseif ($price && $price->amount > 0)
                    @auth
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
                                class="inline-flex w-full items-center justify-center rounded-lg bg-zinc-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                            >
                                خرید محصول
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login', [
                                'redirect' => route(
                                    'store.written-sample-questions.show',
                                    ['product' => $product->slug],
                                ),
                            ]) }}"
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

                @unless ($this->hasAccess)
                    <p class="mt-3 text-center text-xs leading-6 text-zinc-500 dark:text-zinc-400">
                        پس از ثبت سفارش و تأیید پرداخت، محتوای محصول در دسترس شما قرار می‌گیرد.
                    </p>
                @endunless
            </div>
        </aside>

    </div>

    {{-- Purchased product content --}}
    @if ($this->hasAccess)
        <section
            id="product-content"
            class="mt-12 space-y-8"
            aria-labelledby="product-content-title"
        >
            <div>
                <h2
                    id="product-content-title"
                    class="text-2xl font-bold text-zinc-900 dark:text-white"
                >
                    محتوای محصول
                </h2>

                <p class="mt-2 leading-7 text-zinc-600 dark:text-zinc-300">
                    سؤالات و گزینه‌های مربوط به استانداردهای این محصول
                </p>
            </div>

            @forelse ($this->standardsWithQuestions as $standard)
                <section
                    wire:key="standard-{{ $standard->id }}"
                    class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 sm:p-8"
                >
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white">
                        {{ $standard->name_fa }}
                    </h3>

                    @if ($standard->field)
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            رشته: {{ $standard->field->title }}
                        </p>
                    @endif

                    <div class="mt-6 space-y-8">
                        @forelse ($standard->chapters as $chapter)
                            <section
                                wire:key="chapter-{{ $chapter->id }}"
                                class="border-t border-zinc-200 pt-6 dark:border-zinc-700"
                            >
                                <h4 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    فصل {{ $chapter->number }}:
                                    {{ $chapter->title }}
                                </h4>

                                <div class="mt-5 space-y-6">
                                    @forelse ($chapter->questions as $question)
                                        <article
                                            wire:key="question-{{ $question->id }}"
                                            class="rounded-xl bg-zinc-50 p-5 dark:bg-zinc-800/70"
                                        >
                                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">
                                                    سؤال {{ $loop->iteration }}
                                                </span>

                                                @if ($question->type)
                                                    <span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                                        {{ $question->type->label() }}
                                                    </span>
                                                @endif

                                                @if ($question->is_frequently_final)
                                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                                        پرتکرار در آزمون نهایی
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="leading-8 text-zinc-900 dark:text-white">
                                                {{ $question->text }}
                                            </div>

                                            @if ($question->image_path)
                                                <div class="mt-4">
                                                    <img
                                                        src="{{ \Illuminate\Support\Facades\Storage::url($question->image_path) }}"
                                                        alt="تصویر سؤال"
                                                        loading="lazy"
                                                        class="max-h-96 max-w-full rounded-lg object-contain"
                                                    >
                                                </div>
                                            @endif

                                            @if ($question->code)
                                                <pre class="mt-4 overflow-x-auto rounded-lg bg-zinc-900 p-4 text-sm text-zinc-100" dir="ltr"><code>{{ $question->code }}</code></pre>
                                            @endif

                                            @if ($question->options->isNotEmpty())
                                                <ol class="mt-5 space-y-3">
                                                    @foreach ($question->options as $option)
                                                        <li
                                                            wire:key="option-{{ $option->id }}"
                                                            class="rounded-lg border p-3 {{ $option->is_correct
                                                                ? 'border-emerald-300 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950/30'
                                                                : 'border-zinc-200 dark:border-zinc-700' }}"
                                                        >
                                                            <div class="flex items-start gap-3">
                                                                <span class="font-semibold text-zinc-500">
                                                                    {{ $loop->iteration }}.
                                                                </span>

                                                                <div class="min-w-0 flex-1">
                                                                    <div class="text-zinc-800 dark:text-zinc-200">
                                                                        {{ $option->text }}
                                                                    </div>

                                                                    @if ($option->image_path)
                                                                        <img
                                                                            src="{{ \Illuminate\Support\Facades\Storage::url($option->image_path) }}"
                                                                            alt="تصویر گزینه"
                                                                            loading="lazy"
                                                                            class="mt-3 max-h-48 max-w-full rounded-lg object-contain"
                                                                        >
                                                                    @endif
                                                                </div>

                                                                @if ($option->is_correct)
                                                                    <span class="shrink-0 text-xs font-medium text-emerald-700 dark:text-emerald-300">
                                                                        پاسخ صحیح
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @endif

                                            @if ($question->explanation)
                                                <details class="mt-5 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                                    <summary class="cursor-pointer font-medium text-zinc-800 dark:text-zinc-200">
                                                        توضیح پاسخ
                                                    </summary>

                                                    <div class="mt-3 leading-8 text-zinc-600 dark:text-zinc-300">
                                                        {{ $question->explanation }}
                                                    </div>
                                                </details>
                                            @endif
                                        </article>
                                    @empty
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                            هنوز سؤالی برای این فصل ثبت نشده است.
                                        </p>
                                    @endforelse
                                </div>
                            </section>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                هنوز فصلی برای این استاندارد ثبت نشده است.
                            </p>
                        @endforelse
                    </div>
                </section>
            @empty
                <div class="rounded-xl border border-zinc-200 bg-white p-6 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    محتوایی برای نمایش در این محصول ثبت نشده است.
                </div>
            @endforelse
        </section>
    @endif

</div>
