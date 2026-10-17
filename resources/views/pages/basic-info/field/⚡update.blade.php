<?php

use App\Models\Field;
use App\Rules\PersianName;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    public Field $field;

    public string $title = '';
    public string $slug = '';

    public function mount(): void
    {
        $this->title = $this->field->title;
        $this->slug = $this->field->slug;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'min:2', 'max:60', new PersianName, Rule::unique('fields', 'title')->ignore($this->field)],
            'slug' => ['required', 'min:2', 'max:80', 'regex:/\A[a-zA-Z-]+\z/', Rule::unique('fields', 'slug')->ignore($this->field)],
        ];
    }

    public function update(): void
    {
        $validated = $this->validate();

        $this->field->update($validated);

        $this->modal('edit-field-'.$this->field->id)->close();
        $this->dispatch('field-updated', id: $this->field->id);
        Flux::toast(
            text: 'رشته '.$this->title.' با موفقیت ویرایش شد.',
            heading: 'تغییرات اعمال شد.',
            variant: 'warning',
            position: 'top right'
        );

    }

    public function reset_prop(): void
    {
        $this->resetValidation();
    }

};
?>

<div>
    <flux:tooltip content="ویرایش رشته" position="bottom">
        <flux:button
            icon="pencil-square"
            size="xs"
            variant="subtle"
            color="amber"
            class="cursor-pointer transition-colors duration-200"
            x-on:click="$flux.modal('edit-field-{{ $field->id }}').show()"
        />
    </flux:tooltip>

    <flux:modal name="edit-field-{{ $field->id }}" :show="$errors->isNotEmpty()" focusable class="md:w-96" flyout :dismissible="false" @close="reset_prop">
        <div class="space-y-6">
            <flux:heading size="lg">{{__('ویرایش رشته ')}}
                <span class="font-bold text-yellow-500">{{ $field->title }}</span>
            </flux:heading>
            <flux:text class="mt-2">{{__('اطلاعات رشته را ویرایش کنید.')}}</flux:text>

            <form wire:submit.prevent="update" class="space-y-4 flex flex-col gap-3" autocomplete="off">

                <x-my.flt_lbl
                    name="title"
                    label="عنوان رشته"
                    type="text"
                    inputmode="text"
                    required
                    maxlength="60"
                    autofocus
                    class="py-6 font-semibold text-base!"
                />

                <x-my.flt_lbl
                    name="slug"
                    label="عنوان اسلاگ"
                    type="text"
                    inputmode="text"
                    dir="ltr"
                    required
                    maxlength="80"
                    class="py-6 font-semibold text-base!"
                />
                <flux:spacer/>
                <flux:button type="submit" variant="primary" color="yellow" size="sm"
                             class="cursor-pointer">
                    {{__('ویرایش')}}
                </flux:button>

            </form>
        </div>
    </flux:modal>
</div>
