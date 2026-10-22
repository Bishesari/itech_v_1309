<div>
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-2xl
                       bg-blue-100 text-blue-600
                       dark:bg-blue-500/15 dark:text-blue-400"
            >
                <flux:icon.clipboard-document-list
                    variant="outline"
                    class="size-7"
                />
            </div>

            <div class="min-w-0 flex-1">
                <flux:heading size="xl" level="1">
                    بانک سؤال‌ها
                </flux:heading>

                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    مدیریت و جست‌وجوی سؤال‌های ثبت‌شده در استانداردهای آموزشی
                </flux:text>
            </div>

            <flux:button
                icon="plus"
                variant="primary"
                :href="route('question.create', array_filter([
                    'field_id' => $fieldId,
                    'standard_id' => $standardId,
                    'chapter_id' => $chapterId,
                ], fn ($value) => $value !== ''))"
                wire:navigate
            >
                سؤال جدید
            </flux:button>
        </div>
    </div>

    <div class="mb-5 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
        <div class="mb-4 flex items-center justify-between gap-3">
            <flux:heading size="lg">
                فیلتر سؤال‌ها
            </flux:heading>

            <flux:button
                variant="subtle"
                size="sm"
                icon="x-mark"
                wire:click="clearFilters"
            >
                حذف فیلترها
            </flux:button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <flux:field>
                    <flux:label>رشته آموزشی</flux:label>

                    <flux:select
                        wire:model.live="fieldId"
                        placeholder="همه رشته‌ها"
                    >
                        <flux:select.option value="">
                            همه رشته‌ها
                        </flux:select.option>

                        @foreach ($this->fields as $field)
                            <flux:select.option value="{{ $field->id }}">
                                {{ $field->title }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>استاندارد</flux:label>

                    <flux:select
                        wire:model.live="standardId"
                        placeholder="همه استانداردها"
                    >
                        <flux:select.option value="">
                            همه استانداردها
                        </flux:select.option>

                        @foreach ($this->standards as $standard)
                            <flux:select.option value="{{ $standard->id }}">
                                {{ $standard->code }} - {{ $standard->name_fa }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>فصل</flux:label>

                    <flux:select
                        wire:model.live="chapterId"
                        placeholder="همه فصل‌ها"
                    >
                        <flux:select.option value="">
                            همه فصل‌ها
                        </flux:select.option>

                        @foreach ($this->chapters as $chapter)
                            <flux:select.option value="{{ $chapter->id }}">
                                فصل {{ $chapter->number }} - {{ $chapter->title }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
        </div>
    </div>

    <flux:separator variant="subtle" class="mb-4" />

    <flux:table :paginate="$this->questions" class="inline">
        <flux:table.columns>
            <flux:table.column
                sortable
                align="center"
                :sorted="$sortBy === 'id'"
                :direction="$sortDirection"
                wire:click="sort('id')"
            >{{__('شناسه')}}</flux:table.column>

            <flux:table.column align="center">{{__('استاندارد')}}</flux:table.column>

            <flux:table.column align="center">{{__('فصل')}}</flux:table.column>

            <flux:table.column align="center">{{__('متن سؤال')}}</flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'type'"
                :direction="$sortDirection"
                wire:click="sort('type')"
            >
                نوع سؤال
            </flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'difficulty'"
                :direction="$sortDirection"
                wire:click="sort('difficulty')"
            >
                دشواری
            </flux:table.column>

            <flux:table.column
                sortable
                align="center"
                :sorted="$sortBy === 'is_frequently_final'"
                :direction="$sortDirection"
                wire:click="sort('is_frequently_final')"
            >
                پرتکرار
            </flux:table.column>

            <flux:table.column
                sortable
                align="center"
                :sorted="$sortBy === 'is_active'"
                :direction="$sortDirection"
                wire:click="sort('is_active')"
            >{{__('وضعیت')}}</flux:table.column>

            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >
                تاریخ ثبت
            </flux:table.column>

            <flux:table.column align="center">
                عملیات
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->questions as $question)
                <flux:table.row :key="$question->id">
                    <flux:table.cell align="center">
                        <span class="font-mono">
                            {{ $question->id }}
                        </span>
                    </flux:table.cell>

                    <flux:table.cell align="center" >

                            <div>{{ $question->chapter->standard->name_fa }}</div>
                            <div class="mt-1 text-xs text-zinc-500">{{__('کد : ')}} <span class="font-mono">{{ $question->chapter->standard->code }}</span></div>
                            <div class="mt-1 text-xs text-zinc-500">{{__('رشته : ')}}{{ $question->chapter->standard->field->title }}</div>

                    </flux:table.cell>

                    <flux:table.cell class="max-w-44 whitespace-normal wrap-break-word">
                        {{ $question->chapter->number }} {{__(' - ')}}  {{ $question->chapter->title }}
                    </flux:table.cell>

                    <flux:table.cell class="max-w-72 whitespace-normal wrap-break-word">
                        {{ \Illuminate\Support\Str::limit($question->text, 120) }}

                        @if ($question->image_path || $question->code)
                            <div class="mt-1 flex gap-2 text-xs text-zinc-500">
                                @if ($question->image_path)
                                    <span>دارای تصویر</span>
                                @endif

                                @if ($question->code)
                                    <span>دارای کد</span>
                                @endif
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $question->type?->label() ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $question->difficulty?->label() ?? '—' }}
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($question->is_frequently_final)
                            <flux:badge color="green" size="sm">{{__('بله')}}</flux:badge>
                        @else
                            <span class="text-zinc-400">{{__('خیر')}}</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($question->is_active)
                            <flux:badge color="green" size="sm">{{__('فعال')}}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{__('غیر فعال')}}</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$question->jalali_created_at" />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <div class="inline-flex items-center justify-center">

                            <flux:tooltip content="{{ __('نمایش سؤال') }}" position="bottom">
                                <flux:button
                                    icon="eye"
                                    variant="subtle"
                                    color="blue"
                                    :href="route('question.show', $question)"
                                    wire:navigate
                                />
                            </flux:tooltip>

                            <flux:tooltip content="{{ __('ویرایش سؤال') }}" position="bottom">
                                <flux:button
                                    icon="pencil-square"
                                    variant="subtle"
                                    color="blue"
                                    :href="route('question.edit', $question)"
                                    wire:navigate
                                />
                            </flux:tooltip>

                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="10">
                        <div class="py-10 text-center">
                            <flux:icon.document-magnifying-glass
                                variant="outline"
                                class="mx-auto mb-3 size-10 text-zinc-400"
                            />

                            <flux:heading size="lg">
                                سؤالی پیدا نشد
                            </flux:heading>

                            <flux:text class="mt-2 text-sm text-zinc-500">
                                با تغییر فیلترها می‌توانی سؤال‌های دیگری را بررسی کنی.
                            </flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
