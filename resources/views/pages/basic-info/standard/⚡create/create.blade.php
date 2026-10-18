<div>
    <flux:modal.trigger name="new-standard">
        <flux:button
            variant="outline"
            color="blue"
            class="cursor-pointer"
        >
            استاندارد جدید
        </flux:button>
    </flux:modal.trigger>

    <flux:modal
        name="new-standard"
        class="md:w-120"
        align="center"
        :dismissible="false"
        @close="resetForm"
    >
        <div class="space-y-5">
            <flux:heading size="lg" class="text-blue-500 font-bold">
                ثبت استاندارد جدید
            </flux:heading>

            <flux:separator variant="subtle" />

            <form
                wire:submit="save"
                class="grid gap-y-4"
                autocomplete="off"
            >
                <x-my.flt_lbl
                    name="code"
                    label="کد استاندارد"
                    dir="ltr"
                    class="tracking-widest font-semibold"
                    required
                    autofocus
                />

                <div class="space-y-1">
                    <flux:label>رشته آموزشی</flux:label>

                    <flux:select
                        variant="listbox"
                        searchable
                        placeholder="انتخاب رشته آموزشی"
                        wire:model="field_id"
                        required
                    >
                        @foreach ($this->fields as $field)
                            <flux:select.option
                                value="{{ $field->id }}"
                            >
                                {{ $field->title }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    @error('field_id')
                    <flux:text color="red" size="sm">
                        {{ $message }}
                    </flux:text>
                    @enderror
                </div>

                <x-my.flt_lbl
                    name="name_fa"
                    label="نام فارسی"
                    required
                />

                <x-my.flt_lbl
                    name="name_en"
                    label="نام لاتین"
                    dir="ltr"
                    required
                />

                <flux:separator variant="subtle" />

                <flux:heading size="sm">
                    ساعات آموزشی
                </flux:heading>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <x-my.flt_lbl
                        name="nazari_h"
                        label="نظری"
                        dir="ltr"
                        wire:model.live="nazari_h"
                        required
                    />

                    <x-my.flt_lbl
                        name="amali_h"
                        label="عملی"
                        dir="ltr"
                        wire:model.live="amali_h"
                        required
                    />

                    <x-my.flt_lbl
                        name="karvarzi_h"
                        label="کارورزی"
                        dir="ltr"
                        wire:model.live="karvarzi_h"
                        required
                    />

                    <x-my.flt_lbl
                        name="project_h"
                        label="پروژه"
                        dir="ltr"
                        wire:model.live="project_h"
                        required
                    />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <x-my.flt_lbl
                        name="sum_h"
                        label="مجموع ساعات"
                        dir="ltr"
                        class="tracking-widest font-semibold"
                        disabled
                    />

                    <x-my.flt_lbl
                        name="required_h"
                        label="ساعات موردنیاز"
                        dir="ltr"
                        required
                    />
                </div>

                <flux:text size="sm">
                    مجموع ساعات با تغییر مقادیر بالا به‌صورت خودکار محاسبه می‌شود.
                </flux:text>

                <flux:separator variant="subtle" />

                <div class="flex justify-between gap-2 flex-row-reverse">
                    <flux:button
                        type="submit"
                        variant="outline"
                        color="blue"
                        class="cursor-pointer"
                    >
                        ثبت استاندارد
                    </flux:button>

                    <flux:modal.close>
                        <flux:button
                            type="button"
                            variant="ghost"
                            class="cursor-pointer"
                        >
                            انصراف
                        </flux:button>
                    </flux:modal.close>
                </div>
            </form>



        </div>
    </flux:modal>
</div>
