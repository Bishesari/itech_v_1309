<div>
    <flux:modal.trigger name="edit-chapter-{{ $chapter->id }}">
        <flux:button
            variant="subtle"
            color="amber"
            size="sm"
            icon="pencil-square"
            class="cursor-pointer"
            tooltip="ویرایش فصل"
        />
    </flux:modal.trigger>

    <flux:modal
        name="edit-chapter-{{ $chapter->id }}"
        class="md:w-120"
        :dismissible="false"
        @close="$wire.resetFormValidation()"
    >
        <div class="space-y-5">
            <flux:heading size="lg" class="text-amber-500 font-bold">
                ویرایش فصل
            </flux:heading>

            <flux:separator variant="subtle" />

            <form wire:submit="save" class="grid gap-y-4" autocomplete="off">
                <x-my.flt_lbl
                    name="number"
                    label="شماره فصل"
                    dir="ltr"
                    class="tracking-widest font-semibold"
                    required
                    autofocus
                />

                <x-my.flt_lbl
                    name="title"
                    label="عنوان فصل"
                    required
                />

                <flux:separator variant="subtle" />

                <div class="flex justify-between gap-2 flex-row-reverse">
                    <flux:button
                        type="submit"
                        variant="outline"
                        color="amber"
                        class="cursor-pointer"
                        wire:loading.attr="disabled"
                    >
                        ذخیره تغییرات
                    </flux:button>

                    <flux:modal.close>
                        <flux:button type="button" variant="ghost">
                            انصراف
                        </flux:button>
                    </flux:modal.close>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
