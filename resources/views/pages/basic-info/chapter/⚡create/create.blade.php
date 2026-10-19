<div>
    <flux:modal.trigger name="new-chapter">
        <flux:button
            variant="outline"
            color="blue"
            class="cursor-pointer"
        >
            فصل جدید
        </flux:button>
    </flux:modal.trigger>

    <flux:modal
        name="new-chapter"
        class="md:w-120"
        align="center"
        :dismissible="false"
        @close="resetForm"
    >
        <div class="space-y-5">
            <flux:heading size="lg" class="font-bold text-blue-500">
                ثبت فصل جدید
            </flux:heading>

            <flux:separator variant="subtle" />

            <form
                wire:submit="save"
                class="grid gap-y-4"
                autocomplete="off"
            >
                <x-my.flt_lbl
                    name="number"
                    label="شماره فصل"
                    dir="ltr"
                    required
                    autofocus
                />

                <x-my.flt_lbl
                    name="title"
                    label="عنوان فصل"
                    required
                />

                <flux:separator variant="subtle" />

                <div class="flex flex-row-reverse justify-between gap-2">
                    <flux:button
                        type="submit"
                        variant="outline"
                        color="blue"
                        class="cursor-pointer"
                        wire:loading.attr="disabled"
                    >
                        ثبت فصل
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
