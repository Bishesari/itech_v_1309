<div>
    <flux:modal name="exam-blueprint" class="md:w-3xl" variant="flyout">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">مدیریت الگوی سؤال‌ها</flux:heading>
                <flux:text class="mt-1">
                    {{ $examTitle }}
                </flux:text>
                <flux:text class="mt-1">
                    تعداد کل سؤال‌های آزمون: {{ $questionCount }}
                </flux:text>
            </div>

            @if ($requiredChapterCoverage)
                <flux:callout icon="information-circle" color="blue">
                    در این نوع آزمون باید از تمام فصل‌های استاندارد سؤال داشته باشید.
                </flux:callout>
            @else
                <flux:text>
                    فصل‌های موردنظر را انتخاب کنید. لازم نیست همه فصل‌های استاندارد در آزمون حضور داشته باشند.
                </flux:text>
            @endif

            <div class="space-y-4">
                @forelse ($rules as $index => $rule)
                    <div
                        wire:key="blueprint-rule-{{ $index }}"
                        class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700"
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <flux:heading size="sm">
                                ردیف {{ $index + 1 }}
                            </flux:heading>

                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                icon="trash"
                                color="red"
                                wire:click="removeRule({{ $index }})"
                            >
                                حذف
                            </flux:button>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <flux:field>
                                <flux:label>فصل</flux:label>
                                <flux:select
                                    wire:model="rules.{{ $index }}.chapter_id"
                                    placeholder="انتخاب فصل"
                                >
                                    @foreach ($chapters as $chapter)
                                        <flux:select.option value="{{ $chapter->id }}">
                                            {{ $chapter->number }} - {{ $chapter->title }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="rules.{{ $index }}.chapter_id" />
                            </flux:field>

                            <flux:field>
                                <flux:label>سطح دشواری</flux:label>
                                <flux:select wire:model="rules.{{ $index }}.difficulty">
                                    @foreach ($difficulties as $difficulty)
                                        <flux:select.option value="{{ $difficulty->value }}">
                                            {{ $difficulty->label() }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="rules.{{ $index }}.difficulty" />
                            </flux:field>

                            <flux:field>
                                <flux:label>تعداد سؤال</flux:label>
                                <flux:input
                                    type="number"
                                    min="1"
                                    wire:model="rules.{{ $index }}.question_count"
                                />
                                <flux:error name="rules.{{ $index }}.question_count" />
                            </flux:field>
                        </div>
                    </div>
                @empty
                    <flux:text>
                        هنوز ردیفی برای الگوی سؤال‌ها تعریف نشده است.
                    </flux:text>
                @endforelse
            </div>

            <flux:error name="rules" />

            <div class="flex flex-wrap items-center justify-between gap-3">
                <flux:button
                    type="button"
                    variant="outline"
                    icon="plus"
                    wire:click="addRule"
                >
                    افزودن ردیف
                </flux:button>

                <div class="flex gap-2">
                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="$flux.modal('exam-blueprint').close()"
                    >
                        انصراف
                    </flux:button>

                    <flux:button
                        type="button"
                        variant="primary"
                        wire:click="save"
                    >
                        ذخیره الگو
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
