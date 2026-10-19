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
            >
                شناسه
            </flux:table.column>

            <flux:table.column>
                رشته / استاندارد
            </flux:table.column>

            <flux:table.column>
                فصل
            </flux:table.column>

            <flux:table.column>
                متن سؤال
            </flux:table.column>

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
            >
                وضعیت
            </flux:table.column>

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

                    <flux:table.cell>
                        <div class="min-w-36">
                            <div class="text-sm">
                                {{ $question->chapter->standard->field->title }}
                            </div>

                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $question->chapter->standard->code }}
                                -
                                {{ $question->chapter->standard->name_fa }}
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="min-w-32">
                            <span class="text-xs text-zinc-500">
                                فصل {{ $question->chapter->number }}
                            </span>

                            <div class="mt-1">
                                {{ $question->chapter->title }}
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="max-w-72 whitespace-normal break-words">
                            {{ \Illuminate\Support\Str::limit($question->text, 120) }}
                        </div>

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

                    <flux:table.cell align="center">
                        @if ($question->is_frequently_final)
                            <flux:badge color="green" size="sm">
                                بله
                            </flux:badge>
                        @else
                            <span class="text-zinc-400">خیر</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        @if ($question->is_active)
                            <flux:badge color="green" size="sm">
                                فعال
                            </flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">
                                غیرفعال
                            </flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            <x-my.jalali_cell :date="$question->jalali_created_at" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <div class="inline-flex items-center gap-2">
                            {{-- دکمه‌های ویرایش و حذف را در مرحله بعد اضافه می‌کنیم. --}}
                            <flux:button
                                size="xs"
                                variant="subtle"
                                icon="eye"
                                disabled
                                tooltip="نمایش سؤال"
                            />
                            <flux:button
                                :href="route('question.edit', $question)"
                                wire:navigate
                                icon="pencil-square"
                            >
                                ویرایش سؤال
                            </flux:button>

                            <flux:link
                                :href="route('question.show', $question)"
                                wire:navigate
                            >
                                {{ $question->text }}
                            </flux:link>
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
