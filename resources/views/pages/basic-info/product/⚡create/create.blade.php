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
                    ایجاد محصول جدید
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    ثبت اطلاعات محصول و قیمت اولیه
                </flux:text>
            </div>
        </div>

        <div class="mt-5">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item
                    href="{{ route('field.index') }}"
                    wire:navigate
                >
                    رشته‌های آموزشی
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item
                    href="{{ route('product.index') }}"
                    wire:navigate
                >
                    مدیریت محصولات
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    محصول جدید
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <form
        wire:submit="save"
        class="mt-5 space-y-6"
        autocomplete="off"
    >
        <flux:card class="space-y-5">
            <flux:heading size="lg">
                اطلاعات اصلی محصول
            </flux:heading>

            <flux:input
                wire:model="title"
                label="عنوان محصول"
                placeholder="مثلاً نمونه سؤالات آزمون نهایی لاراول"
                required
                autofocus
            />

            <flux:input
                wire:model="slug"
                label="اسلاگ انگلیسی"
                description="فقط حروف انگلیسی و خط تیره مجاز است."
                dir="ltr"
                placeholder="laravel-frequently-final-questions"
                required
            />

            <flux:field>
                <flux:label>نوع محصول</flux:label>

                <flux:select wire:model.live="type" required>
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
                    <flux:label>استاندارد مربوطه</flux:label>

                    <flux:select
                        wire:model="standardId"
                        placeholder="استاندارد را انتخاب کنید"
                        required
                    >
                        @foreach ($this->standards as $standard)
                            <flux:select.option
                                value="{{ $standard->id }}"
                            >
                                {{ $standard->name_fa }}
                                ({{ $standard->code }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="standardId" />
                </flux:field>
            @endif

            <flux:textarea
                wire:model="short_description"
                label="توضیح کوتاه"
                placeholder="توضیح مختصر درباره محصول"
                rows="2"
            />

            <flux:textarea
                wire:model="description"
                label="توضیحات کامل"
                placeholder="توضیحات کامل محصول"
                rows="5"
            />
        </flux:card>

        <flux:card class="space-y-5">
            <flux:heading size="lg">
                قیمت اولیه
            </flux:heading>

            <flux:input
                wire:model="amount"
                label="مبلغ به ریال"
                description="این مبلغ به‌عنوان اولین سابقه قیمت محصول ذخیره می‌شود."
                type="number"
                min="1"
                step="1"
                dir="ltr"
                required
            />

            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                قیمت از زمان ثبت معتبر خواهد بود و تاریخ پایان ندارد.
            </flux:text>
        </flux:card>

        <flux:card class="space-y-5">
            <flux:heading size="lg">
                تنظیمات موتور جستجو (SEO)
            </flux:heading>

            <flux:input
                wire:model="seo_title"
                label="عنوان SEO"
                maxlength="200"
            />

            <flux:textarea
                wire:model="seo_description"
                label="توضیحات SEO"
                maxlength="320"
                rows="3"
            />
        </flux:card>

        <flux:card>
            <flux:checkbox
                wire:model="is_active"
                label="فعال و قابل نمایش در فروشگاه باشد"
                description="در صورت غیرفعال بودن، محصول به‌صورت پیش‌فرض منتشر نمی‌شود."
            />
        </flux:card>

        <div class="flex flex-row-reverse justify-between gap-2">
            <flux:button
                type="submit"
                variant="outline"
                color="blue"
                class="cursor-pointer"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">
                    ثبت محصول
                </span>

                <span wire:loading wire:target="save">
                    در حال ثبت...
                </span>
            </flux:button>

            <flux:button
                href="{{ route('product.index') }}"
                wire:navigate
                variant="ghost"
                class="cursor-pointer"
            >
                انصراف
            </flux:button>
        </div>
    </form>
</div>
