<?php

use App\Models\Field;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    public string $title = '';

    public bool $show_btn = true;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'min:2', Rule::unique('fields', 'title')],
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

                <flux:input
                    wire:model="title"
                    type="text"
                    inputmode="text"
                    required
                    maxlength="50"
                    input:class="text-center py-6 font-semibold text-base!"
                    autocomplete="off"
                    autofocus
                />

                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary" color="teal" size="sm"
                                 class="cursor-pointer">{{__('ثبت')}}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
