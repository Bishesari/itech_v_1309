<?php

use App\Models\Field;
use Livewire\Component;

new class extends Component
{
    public Field $field;

    public function delete(): void
    {
        // بررسی مجدد رابطه، مستقل از وضعیت دکمه در رابط کاربری
        if ($this->field->standards()->exists()) {
            Flux::toast(
                heading: 'امکان حذف وجود ندارد.',
                text: 'این رشته دارای استاندارد مرتبط است.',
                variant: 'danger',
                position: 'top right'
            );

            return;
        }

        $fieldTitle = $this->field->title;
        $fieldId = $this->field->id;

        $this->field->delete();

        $this->modal('delete-field-'.$fieldId)->close();

        $this->dispatch('field-deleted');

        Flux::toast(
            text: "رشته {$fieldTitle} با موفقیت حذف شد.",
            heading: 'حذف شد.',
            variant: 'danger',
            position: 'top right'
        );
    }
};
?>

<div class="text-center">
    <flux:tooltip content="حذف رشته" position="bottom">
        <flux:button
            icon="trash"
            size="xs"
            variant="subtle"
            color="red"
            class="cursor-pointer transition-colors duration-200"
            x-on:click="$flux.modal('delete-field-{{ $field->id }}').show()"
        />
    </flux:tooltip>

    <flux:modal
        name="delete-field-{{ $field->id }}"
        focusable
        class="md:w-96"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('حذف رشته') }}
                    <span class="font-bold text-red-500 dark:text-red-400">
                        {{ $field->title }}
                    </span>
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('با تأیید حذف، اطلاعات رشته حذف خواهد شد.') }}
                </flux:text>
            </div>

            <form wire:submit="delete" class="space-y-4" autocomplete="off">
                <div class="flex">
                    <flux:spacer />

                    <flux:button
                        type="submit"
                        variant="primary"
                        color="red"
                        size="sm"
                        class="cursor-pointer"
                    >
                        {{ __('تأیید حذف') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
