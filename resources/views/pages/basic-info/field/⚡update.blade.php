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
            'slug' => ['required', 'min:2', 'max:80', 'alpha_dash:ascii', Rule::unique('fields', 'slug')->ignore($this->field)],
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

};
?>

<div>
    <flux:tooltip content="ویرایش رشته" position="bottom">
        <flux:button icon="pencil-square" size="xs" variant="primary" color="yellow" class="cursor-pointer"
                     x-on:click="$flux.modal('edit-field-{{ $field->id }}').show()">
        </flux:button>
    </flux:tooltip>

    <flux:modal name="edit-field-{{ $field->id }}" :show="$errors->isNotEmpty()" focusable class="md:w-96" flyout :dismissible="false">
        <div class="space-y-6">
            <flux:heading size="lg">{{__('ویرایش رشته ')}}
                <span class="font-bold text-yellow-500">{{ $field->title }}</span>
            </flux:heading>
            <flux:text class="mt-2">{{__('اطلاعات رشته را ویرایش کنید.')}}</flux:text>

            <form wire:submit.prevent="update" class="space-y-4 flex flex-col gap-3" autocomplete="off">
                <flux:field>
                    <flux:label badge="فارسی" class="text-xs font-light!">
                        {{ __('عنوان رشته') }}
                    </flux:label>
                    <flux:input wire:model="title" type="text" inputmode="text" required maxlength="60" autofocus
                                input:class="text-center py-6 font-semibold text-base!"
                    />
                    <flux:error
                        name="title"
                        class="-mt-1! text-xs font-light!"
                    />
                </flux:field>
                <flux:field>
                    <flux:label badge="لاتین" class="text-xs font-light!">
                        {{ __('عنوان اسلاگ') }}
                    </flux:label>
                    <flux:input wire:model="slug" type="text" inputmode="text" required maxlength="80"
                                input:class="text-center py-6 font-semibold text-base!" dir="ltr"
                    />
                    <flux:error
                        name="slug"
                        class="-mt-1! text-xs font-light!"
                    />
                </flux:field>
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary" color="yellow" size="sm"
                                 class="cursor-pointer">
                        {{__('ویرایش')}}
                    </flux:button>

            </form>
        </div>
    </flux:modal>
</div>
