<div>

    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                <flux:icon.academic-cap variant="outline" class="size-7" />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    {{ __('رشته‌های آموزشی') }}
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('مدیریت و سازمان‌دهی رشته‌های آموزشی مؤسسه') }}
                </flux:text>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>
                    {{ __('اطلاعات پایه') }}
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    {{ __('رشته‌های آموزشی') }}
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <livewire:pages::basic-info.field.create />
        </div>
    </div>

    <flux:separator variant="subtle" />

    <flux:table :paginate="$this->fields" class="inline">

        <flux:table.columns>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'id'"
                :direction="$sortDirection"
                wire:click="sort('id')"
            >
                {{ __('#') }}
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'title'"
                :direction="$sortDirection"
                wire:click="sort('title')"
            >
                {{ __('عنوان رشته') }}
            </flux:table.column>

            <flux:table.column
                dir="ltr"
                sortable
                :sorted="$sortBy === 'slug'"
                :direction="$sortDirection"
                wire:click="sort('slug')"
            >
                {{ __('Slug') }}
            </flux:table.column>

            <flux:table.column align="center">
                {{ __('استانداردها') }}
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >
                {{ __('زمان ثبت') }}
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'updated_at'"
                :direction="$sortDirection"
                wire:click="sort('updated_at')"
            >
                {{ __('زمان ویرایش') }}
            </flux:table.column>

            <flux:table.column align="center">
                {{ __('عملیات') }}
            </flux:table.column>
        </flux:table.columns>

        @if ($highlightedId)
            <div
                x-data
                x-init="setTimeout(() => $wire.set('highlightedId', null), 2000)"
            ></div>
        @endif

        <flux:table.rows>
            @foreach ($this->fields as $field)
                @php
                    $class = $highlightedId === $field->id
                        ? 'bg-green-100/80 dark:bg-green-900/30'
                        : '';
                @endphp


                    <flux:table.row
                        :key="$field->id"
                        class="{{ $class }} transition-colors duration-300 hover:bg-zinc-100 dark:hover:bg-stone-900/80"
                    >

                    <flux:table.cell>
                    <span class="text-sm tabular-nums text-zinc-500 dark:text-zinc-400">
                        {{ $field->id }}
                    </span>
                    </flux:table.cell>

                    <flux:table.cell>
                    <span class="font-medium text-zinc-800 dark:text-zinc-100">
                        {{ $field->title }}
                    </span>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div dir="ltr" class="text-left">
                            <span class="inline-block rounded-md bg-zinc-100 px-2 py-1 font-mono text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $field->slug }}
                            </span>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <flux:badge
                            color="green"
                            size="sm"
                            inset="top bottom"
                        >
                            {{ $field->standards_count }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$field->jalali_created_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$field->jalali_updated_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <div class="inline-flex items-center justify-center gap-2">
                            <livewire:pages::basic-info.field.update
                                :$field
                                :key="'field-edit-'.$field->id"
                            />

                            @if ($field->standards_count === 0)
                                <livewire:pages::basic-info.field.delete
                                    :$field
                                    :key="'field-delete-'.$field->id"
                                />
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

</div>
