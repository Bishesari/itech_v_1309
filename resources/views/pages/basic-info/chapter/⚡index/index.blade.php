<div>
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-2xl
                       bg-blue-100 text-blue-600
                       dark:bg-blue-500/15 dark:text-blue-400"
            >
                <flux:icon.list-bullet
                    variant="outline"
                    class="size-7"
                />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    مدیریت فصل‌ها
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $standard->name_fa }}
                </flux:text>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('field.index') }}" wire:navigate>
                    رشته‌های آموزشی
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item
                    href="{{ route('standard.index', ['highlight_id' => $standard->id]) }}"
                    wire:navigate
                >
                    استانداردها
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    {{ $standard->name_fa }}
                </flux:breadcrumbs.item>

                <flux:breadcrumbs.item>
                    فصل‌ها
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <livewire:pages::basic-info.chapter.create :$standard />
        </div>
    </div>

    <flux:separator variant="subtle" />

    <flux:table :paginate="$this->chapters" class="inline">
        <flux:table.columns>
            <flux:table.column
                sortable
                align="center"
                :sorted="$sortBy === 'id'"
                :direction="$sortDirection"
                wire:click="sort('id')"
            >
                شناسه
            </flux:table.column>

            <flux:table.column
                sortable
                align="center"
                :sorted="$sortBy === 'number'"
                :direction="$sortDirection"
                wire:click="sort('number')"
            >
                شماره فصل
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'title'"
                :direction="$sortDirection"
                wire:click="sort('title')"
            >
                عنوان فصل
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >
                زمان ثبت
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'updated_at'"
                :direction="$sortDirection"
                wire:click="sort('updated_at')"
            >
                زمان ویرایش
            </flux:table.column>

            <flux:table.column align="center">
                عملیات
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->chapters as $chapter)
                <flux:table.row
                    :key="$chapter->id"
                    x-data="{
                        highlighted: @js($highlightedId === $chapter->id),
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
                    <flux:table.cell align="center">
                        <span class="font-mono">{{ $chapter->id }}</span>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <span class="font-mono">{{ $chapter->number }}</span>
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $chapter->title }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$chapter->jalali_created_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$chapter->jalali_updated_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <livewire:pages::basic-info.chapter.update
                            :$chapter
                            :key="'chapter-edit-'.$chapter->id"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
