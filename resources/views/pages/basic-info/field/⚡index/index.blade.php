<div>
    <flux:heading size="lg" level="1">
        {{__('اطلاعات پایه')}}
    </flux:heading>

    <div class="inline-flex mt-2 mb-4">
        <flux:text>{{__('رشته های آموزشی')}}</flux:text>

        {{-- Create Component --}}
        <livewire:pages::basic-info.field.create />
    </div>
    <flux:separator variant="subtle"/>

    <flux:table :paginate="$this->fields" class="inline">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                               wire:click="sort('id')">
                {{__('#')}}
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection"
                               wire:click="sort('title')" class="justify-items-center">
                {{__('عنوان رشته')}}
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection"
                               wire:click="sort('title')" dir="ltr" class="justify-items-center">
                {{__('Slug')}}
            </flux:table.column>

            <flux:table.column>{{__('استانداردها')}}</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                               wire:click="sort('created_at')">
                {{__('زمان ثبت')}}
            </flux:table.column>

            <flux:table.column align="center" sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                               wire:click="sort('updated_at')">
                {{__('زمان ویرایش')}}
            </flux:table.column>


            <flux:table.column>{{ __('عملیات') }}</flux:table.column>
        </flux:table.columns>

        @if($highlightedId)
            <div x-data x-init="setTimeout(() => $wire.set('highlightedId', null), 2000)"></div>
        @endif
        <flux:table.rows>

            @foreach ($this->fields as $field)
                @php
                    $class='';
                    if($highlightedId === $field->id){
                        $class='bg-green-100 dark:bg-green-900/40';
                    }
                @endphp

                <flux:table.row class=" {{$class}} dark:hover:bg-stone-900/80 transition duration-300 hover:bg-zinc-100" :key="$field->id">
                    <flux:table.cell>{{ $field->id }}</flux:table.cell>
                    <flux:table.cell>{{ $field->title }}</flux:table.cell>
                    <flux:table.cell dir="ltr">{{ $field->slug }}</flux:table.cell>
                    <flux:table.cell class="text-center">
                        <flux:badge color="green" size="sm"
                                    inset="top bottom">{{ $field->standards_count }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap"><x-my.jalali_cell :date="$field->jalali_created_at" /></flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap"><x-my.jalali_cell :date="$field->jalali_updated_at" /></flux:table.cell>


                    <flux:table.cell>

                        <div class="inline-flex items-center gap-2">
{{--                            <livewire:pages::filed.edit :$field :key="'field-edit-'.$field->id"/>--}}
{{--                            <livewire:pages::filed.delete :$field :key="'field-delete-'.$field->id"/>--}}
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


</div>
