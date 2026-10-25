<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">
                <flux:icon name="clipboard-document-list" class="inline-block size-6 align-middle" />
                مدیریت آزمون‌ها
            </flux:heading>

            <flux:subheading class="mt-2">
                تعریف آزمون‌ها و مدیریت تنظیمات آن‌ها
            </flux:subheading>
        </div>

        <livewire:pages::basic-info.exam.create />
        <livewire:pages::basic-info.exam.edit />
        <livewire:pages::basic-info.exam.blueprint />
    </div>

    <flux:card>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full sm:max-w-sm">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="جست‌وجو بر اساس عنوان یا استاندارد..."
                    icon="magnifying-glass"
                    clearable
                />
            </div>

            <flux:select wire:model.live="perPage" class="w-full sm:w-32">
                <flux:select.option value="10">۱۰ مورد</flux:select.option>
                <flux:select.option value="25">۲۵ مورد</flux:select.option>
                <flux:select.option value="50">۵۰ مورد</flux:select.option>
            </flux:select>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>
                        <button
                            type="button"
                            wire:click="sortBy('title')"
                            class="inline-flex items-center gap-1"
                        >
                            عنوان آزمون
                            <flux:icon name="chevron-up-down" class="size-4" />
                        </button>
                    </flux:table.column>

                    <flux:table.column>استاندارد</flux:table.column>

                    <flux:table.column>
                        <button
                            type="button"
                            wire:click="sortBy('type')"
                            class="inline-flex items-center gap-1"
                        >
                            نوع آزمون
                            <flux:icon name="chevron-up-down" class="size-4" />
                        </button>
                    </flux:table.column>

                    <flux:table.column>
                        <button
                            type="button"
                            wire:click="sortBy('question_count')"
                            class="inline-flex items-center gap-1"
                        >
                            تعداد سؤال
                            <flux:icon name="chevron-up-down" class="size-4" />
                        </button>
                    </flux:table.column>

                    <flux:table.column>مدت آزمون</flux:table.column>

                    <flux:table.column>وضعیت</flux:table.column>

                    <flux:table.column align="end">عملیات</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->exams as $exam)
                        <flux:table.row
                            wire:key="exam-{{ $exam->id }}"
                            class="{{ $highlightedId === $exam->id ? 'bg-green-50 dark:bg-green-950/30' : '' }}"
                        >
                            <flux:table.cell>
                                <div class="font-medium">
                                    {{ $exam->title }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $exam->standard->name_fa ?? '—' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $exam->type->label() }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $exam->question_count }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ intdiv($exam->durationInSeconds(), 60) }} دقیقه
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($exam->is_active)
                                    <flux:badge color="green">فعال</flux:badge>
                                @else
                                    <flux:badge color="zinc">غیرفعال</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        wire:click="$dispatch('edit-exam', { examId: {{ $exam->id }} })"
                                    >
                                        ویرایش
                                    </flux:button>

                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        :href="route('exam.show', $exam)"
                                    >
                                        نمایش
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="py-12 text-center">
                                <flux:icon
                                    name="clipboard-document-list"
                                    class="mx-auto mb-3 size-10 text-zinc-400"
                                />

                                <div class="font-medium">
                                    آزمونی ثبت نشده است.
                                </div>

                                <div class="mt-1 text-sm text-zinc-500">
                                    برای شروع، یک آزمون جدید ایجاد کنید.
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4">
            {{ $this->exams->links() }}
        </div>
    </flux:card>
</div>
