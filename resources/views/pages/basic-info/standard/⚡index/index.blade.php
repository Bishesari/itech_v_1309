<div>
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-2xl
                       bg-blue-100 text-blue-600
                       dark:bg-blue-500/15 dark:text-blue-400"
            >
                <flux:icon.academic-cap
                    variant="outline"
                    class="size-7"
                />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    {{ __('استانداردهای آموزشی') }}
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('مدیریت و سازمان‌دهی استانداردهای آموزشی مؤسسه') }}
                </flux:text>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>
                    {{ __('اطلاعات پایه') }}
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    {{ __('استانداردهای آموزشی') }}
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>

                <livewire:pages::basic-info.standard.create />


        </div>
    </div>

    <flux:separator variant="subtle" />

    <flux:table :paginate="$this->standards" class="inline">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sort('id')" align="center">
                {{__('شناسه')}}
            </flux:table.column>

            <flux:table.column>
                {{ __('رشته آموزشی') }}
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'name_fa'"
                :direction="$sortDirection"
                wire:click="sort('name_fa')"
            >
                {{ __('نام فارسی') }}
            </flux:table.column>

            <flux:table.column align="center">
                {{ __('فصل‌ها') }}
            </flux:table.column>

            <flux:table.column align="center">
                {{ __('سؤالات کتبی') }}
            </flux:table.column>

            <flux:table.column align="center">
                {{ __('سؤالات عملی') }}
            </flux:table.column>


            <flux:table.column align="center"
                sortable
                :sorted="$sortBy === 'code'"
                :direction="$sortDirection"
                wire:click="sort('code')"
            >
                {{ __('کد استاندارد') }}
            </flux:table.column>

            <flux:table.column align="center"
                sortable
                :sorted="$sortBy === 'sum_h'"
                :direction="$sortDirection"
                wire:click="sort('sum_h')"
            >
                {{ __('مجموع ساعات') }}
            </flux:table.column>

            <flux:table.column align="center"
                sortable
                :sorted="$sortBy === 'required_h'"
                :direction="$sortDirection"
                wire:click="sort('required_h')"
            >
                {{ __('ساعات موردنیاز') }}
            </flux:table.column>

            <flux:table.column align="center">
                {{ __('وضعیت') }}
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

        <flux:table.rows>
            @foreach ($this->standards as $standard)
                <flux:table.row
                    :key="$standard->id"
                    x-data="{
                        highlighted: @js($highlightedId === $standard->id),
                        init() {
                            if (this.highlighted) {
                                setTimeout(() => this.highlighted = false, 2000)
                            }
                        }
                    }"
                    x-bind:class="highlighted
                        ? 'bg-green-100/80 dark:bg-green-900/30'
                        : ''"
                    class="transition-colors duration-300 hover:bg-zinc-100 dark:hover:bg-stone-900/80"
                >
                    <flux:table.cell>
                        <flux:heading class="flex items-center gap-1">
                            <flux:tooltip toggleable position="left">
                                <flux:button
                                    icon="information-circle"
                                    size="sm"
                                    variant="ghost" color="blue"
                                    class="cursor-pointer"
                                />

                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                    <p>
                                        {{ __('نام لاتین: ') }}
                                        {{ $standard->name_en }}
                                    </p>

                                    <p>
                                        {{ __('نظری: ') }}{{ $standard->nazari_h }}
                                        {{ __('، عملی: ') }}{{ $standard->amali_h }}
                                        {{ __('، کارورزی: ') }}{{ $standard->karvarzi_h }}
                                        {{ __('، پروژه: ') }}{{ $standard->project_h }}
                                    </p>
                                </flux:tooltip.content>
                            </flux:tooltip>
                            <span class="font-mono text-base">{{$standard->id}}</span>
                        </flux:heading>
                    </flux:table.cell>

                    <flux:table.cell>{{ $standard->field->title }}</flux:table.cell>
                    <flux:table.cell class="max-w-64 whitespace-normal wrap-break-word">{{ $standard->name_fa }}</flux:table.cell>
                    <flux:table.cell align="center">
                        <span class="font-mono text-base">{{ $standard->chapters_count }}</span>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <span class="font-mono text-base">{{ $standard->multiple_choice_questions_count }}</span>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <span class="font-mono text-base">{{ $standard->practical_questions_count }}</span>
                    </flux:table.cell>
                    <flux:table.cell align="center" dir="ltr"><span class="font-mono text-base">{{ $standard->code }}</span></flux:table.cell>
                    <flux:table.cell align="center" dir="ltr"><span class="font-mono text-base">{{ $standard->sum_h }}</span></flux:table.cell>
                    <flux:table.cell align="center" dir="ltr"><span class="font-mono text-base">{{ $standard->required_h }}</span></flux:table.cell>

                    <flux:table.cell align="center">
                        <div class="inline-flex items-center gap-2">
                            <flux:badge
                                size="sm"
                                :color="$standard->is_active ? 'green' : 'red'"
                            >
                                {{ $standard->is_active ? __('فعال') : __('غیرفعال') }}
                            </flux:badge>

                            <flux:switch
                                :checked="$standard->is_active"
                                wire:key="standard-switch-{{ $standard->id }}-{{ (int) $standard->is_active }}"
                                wire:click="toggleStatus({{ $standard->id }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleStatus"
                            />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$standard->jalali_created_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$standard->jalali_updated_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell align="center" >
                        <div class="inline-flex items-center justify-center gap-2">
                            <flux:tooltip content="{{ __('مدیریت فصل‌ها') }}" position="bottom">

                                <flux:button
                                    icon="list-bullet"
                                    size="xs"
                                    variant="subtle"
                                    color="blue"
                                    class="cursor-pointer"
                                    :href="route('chapter.index', $standard)"
                                    wire:navigate
                                />
                            </flux:tooltip>


                            <livewire:pages::basic-info.standard.update :$standard :key="'standard-edit-'.$standard->id"/>

                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
