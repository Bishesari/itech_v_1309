<div>
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl
                        bg-blue-100 text-blue-600
                        dark:bg-blue-500/15 dark:text-blue-400">
                <flux:icon.pencil-square variant="outline" class="size-7" />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    ویرایش محصول
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $product->title }}
                </flux:text>
            </div>
        </div>

        <div class="mt-5">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item
                    href="{{ route('product.index') }}"
                    wire:navigate
                >
                    مدیریت محصولات
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    ویرایش محصول
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <form wire:submit="save" class="mt-5 space-y-6">
        <flux:card class="space-y-5">
            <flux:heading size="lg">اطلاعات اصلی</flux:heading>

            <flux:field>
                <flux:label>عنوان محصول</flux:label>
                <flux:input wire:model="title" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>اسلاگ انگلیسی</flux:label>
                <flux:input wire:model="slug" dir="ltr" />
                <flux:error name="slug" />
            </flux:field>

            <flux:field>
                <flux:label>نوع محصول</flux:label>

                <flux:select wire:model.live="type">
                    <flux:select.option value="written_sample_question">
                        نمونه سؤالات کتبی
                    </flux:select.option>
                    <flux:select.option value="exam_registration">
                        ثبت‌نام آزمون
                    </flux:select.option>
                    <flux:select.option value="software_service">
                        خدمات نرم‌افزاری
                    </flux:select.option>
                    <flux:select.option value="course_tuition">
                        شهریه دوره
                    </flux:select.option>
                </flux:select>

                <flux:error name="type" />
            </flux:field>

            @if ($type === \App\Enums\ProductType::WrittenSampleQuestion->value)
                <flux:field>
                    <flux:label>استاندارد مرتبط</flux:label>

                    <flux:select wire:model="standardId" placeholder="انتخاب استاندارد">
                        @foreach ($this->standards as $standard)
                            <flux:select.option value="{{ $standard->id }}">
                                {{ $standard->name_fa }} ({{ $standard->code }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="standardId" />
                </flux:field>
            @endif

            <flux:field>
                <flux:label>توضیح کوتاه</flux:label>
                <flux:input wire:model="short_description" />
                <flux:error name="short_description" />
            </flux:field>

            <flux:field>
                <flux:label>توضیحات کامل</flux:label>
                <flux:textarea wire:model="description" rows="5" />
                <flux:error name="description" />
            </flux:field>

            <flux:checkbox
                wire:model="is_active"
                label="محصول فعال باشد"
            />
            <flux:error name="is_active" />
        </flux:card>

        <flux:card class="space-y-5">
            <flux:heading size="lg">قیمت‌گذاری</flux:heading>

            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                قیمت را به ریال وارد کنید. در صورت تغییر مبلغ، قیمت قبلی
                در تاریخچه باقی می‌ماند.
            </flux:text>

            <flux:field>
                <flux:label>قیمت فعلی (ریال)</flux:label>
                <flux:input
                    wire:model="amount"
                    type="number"
                    min="1"
                    step="1"
                    dir="ltr"
                />
                <flux:error name="amount" />
            </flux:field>
        </flux:card>
        <flux:card class="space-y-4">
            <flux:heading size="lg">
                تاریخچه قیمت‌ها
            </flux:heading>

            @php
                $prices = $product->prices()
                    ->orderByDesc('starts_at')
                    ->get();
            @endphp

            @if ($prices->isEmpty())
                <flux:text class="text-sm text-zinc-500">
                    هنوز قیمتی برای این محصول ثبت نشده است.
                </flux:text>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column align="end">
                            مبلغ (ریال)
                        </flux:table.column>

                        <flux:table.column>
                            شروع اعتبار
                        </flux:table.column>

                        <flux:table.column>
                            پایان اعتبار
                        </flux:table.column>

                        <flux:table.column align="center">
                            وضعیت
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($prices as $price)
                            <flux:table.row :key="$price->id">
                                <flux:table.cell align="end">
                            <span class="font-medium">
                                {{ number_format($price->amount) }}
                            </span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $price->getJalaliDate('starts_at') }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $price->ends_at
                                        ? $price->getJalaliDate('ends_at')
                                        : '—'
                                    }}
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    @if (
                                        $price->starts_at->lte(now())
                                        && (
                                            $price->ends_at === null
                                            || $price->ends_at->gt(now())
                                        )
                                    )
                                        <flux:badge color="green">
                                            جاری
                                        </flux:badge>
                                    @elseif ($price->starts_at->gt(now()))
                                        <flux:badge color="blue">
                                            آینده
                                        </flux:badge>
                                    @else
                                        <flux:badge color="zinc">
                                            پایان‌یافته
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:card>

        <flux:card class="space-y-5">
            <flux:heading size="lg">تنظیمات سئو</flux:heading>

            <flux:field>
                <flux:label>عنوان سئو</flux:label>
                <flux:input wire:model="seo_title" />
                <flux:error name="seo_title" />
            </flux:field>

            <flux:field>
                <flux:label>توضیحات سئو</flux:label>
                <flux:textarea wire:model="seo_description" rows="3" />
                <flux:error name="seo_description" />
            </flux:field>
        </flux:card>

        <div class="flex flex-wrap gap-3">
            <flux:button type="submit" variant="primary" icon="check">
                ذخیره تغییرات
            </flux:button>

            <flux:button
                href="{{ route('product.index') }}"
                wire:navigate
                variant="ghost"
            >
                انصراف
            </flux:button>
        </div>
    </form>
</div>
