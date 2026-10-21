<div>
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-2xl
                       bg-blue-100 text-blue-600
                       dark:bg-blue-500/15 dark:text-blue-400"
            >
                <flux:icon.shopping-bag
                    variant="outline"
                    class="size-7"
                />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    مدیریت محصولات
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    مدیریت محصولات فروشگاه و قیمت‌گذاری
                </flux:text>
            </div>

            <flux:button
                href="{{ route('product.create') }}"
                wire:navigate
                variant="outline"
                color="blue"
                icon="plus"
            >
                محصول جدید
            </flux:button>
        </div>

        <div class="mt-5">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item
                    href="{{ route('field.index') }}"
                    wire:navigate
                >
                    رشته‌های آموزشی
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    مدیریت محصولات
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="my-4 max-w-md">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="جستجو بر اساس عنوان یا اسلاگ..."
            icon="magnifying-glass"
            clearable
        />
    </div>

    <flux:table :paginate="$this->products" class="inline">
        <flux:table.columns>
            <flux:table.column align="center">
                شناسه
            </flux:table.column>

            <flux:table.column>
                عنوان محصول
            </flux:table.column>

            <flux:table.column>
                نوع محصول
            </flux:table.column>

            <flux:table.column>
                استاندارد
            </flux:table.column>

            <flux:table.column align="end">
                قیمت جاری (ریال)
            </flux:table.column>

            <flux:table.column align="center">
                وضعیت
            </flux:table.column>

            <flux:table.column align="center">
                عملیات
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->products as $product)
                @php
                    $currentPrice = $product->currentPrice();
                @endphp

                <flux:table.row :key="$product->id">
                    <flux:table.cell align="center">
                        <span class="font-mono">
                            {{ $product->id }}
                        </span>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="font-medium">
                            {{ $product->title }}
                        </div>

                        <div
                            dir="ltr"
                            class="mt-1 text-xs text-zinc-500"
                        >
                            {{ $product->slug }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $this->productTypeLabel($product->type) }}
                    </flux:table.cell>

                    <flux:table.cell>
                        @forelse ($product->standards as $standard)
                            <div>{{ $standard->name_fa }}</div>
                        @empty
                            <span class="text-zinc-400">—</span>
                        @endforelse
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        @if ($currentPrice)
                            {{ number_format($currentPrice->amount) }}
                        @else
                            <span class="text-zinc-400">
                                بدون قیمت
                            </span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        @if ($product->is_active)
                            <flux:badge color="green">
                                فعال
                            </flux:badge>
                        @else
                            <flux:badge color="zinc">
                                غیرفعال
                            </flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <flux:button
                            href="{{ route('product.edit', $product) }}"
                            wire:navigate
                            variant="ghost"
                            size="sm"
                            icon="pencil-square"
                        >
                            ویرایش
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
