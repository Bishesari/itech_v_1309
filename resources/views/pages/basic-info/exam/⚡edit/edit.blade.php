<div>
    <flux:modal name="edit-exam" class="w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">ویرایش آزمون</flux:heading>
                <flux:subheading class="mt-1">
                    اطلاعات آزمون انتخاب‌شده را اصلاح کنید.
                </flux:subheading>
            </div>

            <flux:input
                wire:model="title"
                label="عنوان آزمون"
                required
            />

            <flux:field>
                <flux:label>استاندارد</flux:label>
                <flux:select wire:model="standard_id" placeholder="انتخاب استاندارد" required>
                    @foreach ($standards as $standard)
                        <flux:select.option value="{{ $standard->id }}">
                            {{ $standard->name_fa }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="standard_id" />
            </flux:field>

            <flux:field>
                <flux:label>نوع آزمون</flux:label>
                <flux:select wire:model.live="type" required>
                    @foreach ($examTypes as $examType)
                        <flux:select.option value="{{ $examType->value }}">
                            {{ $examType->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            @if ($type === \App\Enums\ExamType::Simulated->value)
                <flux:field>
                    <flux:label>محصول مرتبط</flux:label>
                    <flux:select wire:model="product_id" placeholder="انتخاب محصول شبیه‌سازی" required>
                        @foreach ($products as $product)
                            <flux:select.option value="{{ $product->id }}">
                                {{ $product->title }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="product_id" />
                </flux:field>
            @endif

            <flux:textarea
                wire:model="description"
                label="توضیحات"
                rows="3"
            />


            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model="question_count"
                    type="number"
                    min="1"
                    label="تعداد سؤال"
                    required
                />

                <flux:input
                    wire:model="seconds_per_question"
                    type="number"
                    min="1"
                    max="3600"
                    label="زمان هر سؤال (ثانیه)"
                    required
                />

                <flux:field>
                    <flux:label>روش انتخاب سؤال</flux:label>
                    <flux:select wire:model.live="selection_mode">
                        <flux:select.option value="random">تصادفی</flux:select.option>
                        <flux:select.option value="blueprint">بر اساس الگوی سؤالات</flux:select.option>
                    </flux:select>
                    <flux:error name="selection_mode" />
                </flux:field>

                @if ($selection_mode === 'blueprint' && $examId)
                    <div class="mt-3">
                        <flux:button
                            type="button"
                            variant="outline"
                            icon="clipboard-document-list"
                            wire:click="$dispatch('manage-exam-blueprint', { examId: {{ $examId }} })"
                        >
                            مدیریت الگوی سؤال‌ها
                        </flux:button>
                    </div>
                @endif

                <flux:input
                    wire:model="pass_percentage"
                    type="number"
                    min="0"
                    max="100"
                    label="حدنصاب قبولی (درصد)"
                />

                <flux:input
                    wire:model="max_attempts"
                    type="number"
                    min="1"
                    label="حداکثر دفعات شرکت"
                    placeholder="طبق تنظیم پیش‌فرض"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model="starts_at"
                    type="datetime-local"
                    label="زمان شروع (اختیاری)"
                />

                <flux:input
                    wire:model="ends_at"
                    type="datetime-local"
                    label="زمان پایان (اختیاری)"
                />
            </div>

            <flux:checkbox
                wire:model="is_active"
                label="آزمون فعال باشد"
            />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost" type="button">
                        انصراف
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    ذخیره تغییرات
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
