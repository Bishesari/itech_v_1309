<div class="mx-auto max-w-7xl px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">بانک سؤال‌ها</h1>
        <p class="mt-2 text-sm text-gray-600">
            بانک سؤال استاندارد موردنظرتان را انتخاب کنید.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->products as $product)
            <article wire:key="product-{{ $product->getKey() }}"
                     class="rounded-xl border border-gray-200 p-5">
                <h2 class="font-semibold">
                    {{ $product->title }}
                </h2>

                <div class="mt-3">
                    <h3 class="text-sm font-medium text-gray-700">استانداردها</h3>

                    <ul class="mt-1 space-y-1 text-sm text-gray-600">
                        @foreach ($product->standards as $standard)
                            <li wire:key="standard-{{ $standard->getKey() }}">
                                {{ $standard->title }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <a href="{{ route('question-banks.show', $product) }}" wire:navigate>
                    مشاهده جزئیات
                </a>

            </article>
        @empty
            <p class="text-gray-600">
                در حال حاضر بانک سؤال فعالی برای نمایش وجود ندارد.
            </p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $this->products->links() }}
    </div>
</div>
