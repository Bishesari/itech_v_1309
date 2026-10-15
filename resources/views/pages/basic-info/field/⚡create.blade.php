<?php

use App\Models\Field;
use App\Rules\PersianName;
use App\Support\PersianText;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    public string $title = '';
    public string $slug = '';

    public bool $show_btn = true;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'min:2', 'max:60', new PersianName, Rule::unique('fields', 'title')],
            'slug' => ['required', 'min:2', 'max:80', 'alpha_dash:ascii', Rule::unique('fields', 'slug')],
        ];
    }


    public function save(): void
    {
        $field = Field::query()->create($this->validate());
        $this->modal('new-field')->close();
        $this->dispatch('field-created', id: $field->id);

        Flux::toast(
            heading: 'ثبت شد.',
            text: 'رشته جدید با موفقیت ثبت شد.',
            variant: 'success',
            position: 'top right'
        );
    }

    public function reset_prop(): void
    {
        $this->reset();
        $this->resetErrorBag();
    }
};
?>

<div>
    @if($show_btn)
        <flux:tooltip content="رشته جدید" position="left">
            <flux:icon.plus-circle variant="micro" class="cursor-pointer size-5 text-blue-500 mr-4"
                                   x-on:click="$flux.modal('new-field').show()"/>
        </flux:tooltip>
    @endif
    <flux:modal name="new-field" :show="$errors->isNotEmpty()" focusable class="md:w-96" @close="reset_prop"
                :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{__('درج رشته جدید')}}</flux:heading>
                <flux:text class="mt-2">{{__('اطلاعات رشته آموزشی جدید را وارد کنید.')}}</flux:text>
            </div>

            <form wire:submit.prevent="save" class="space-y-4 flex flex-col gap-3" autocomplete="off">

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


                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary" color="teal" size="sm"
                                 class="cursor-pointer">{{__('ثبت')}}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
