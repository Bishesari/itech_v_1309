<form wire:submit="save" class="space-y-6 sm:w-120" autocomplete="off">
    <div>
        <flux:heading size="lg">ایجاد آزمون جدید</flux:heading>
        <flux:subheading class="mt-1">
            اطلاعات اولیه آزمون را وارد کنید.
        </flux:subheading>
    </div>

    <!-- ExamType select menu ... -->
    <flux:select wire:model.live="exam_type" variant="listbox" placeholder="نوع انتخاب کنید ..."
                 label="نوع آزمون" searchable class="mb-3">
        @foreach ($examTypes as $examType)
            <flux:select.option value="{{$examType->value}}">{{ $examType->label() }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:field>
        <flux:label>روش ارائه سؤال‌ها</flux:label>

        <flux:radio.group wire:model="selection_mode" variant="cards">
            <flux:radio
                value="different"
                label="سؤال‌های متفاوت"
                description="برای هر هنرجو، سؤال‌ها به‌صورت تصادفی انتخاب می‌شوند."
            />

            <flux:radio
                value="same"
                label="سؤال‌های یکسان"
                description="همه هنرجویان یک مجموعه سؤال مشترک دریافت می‌کنند؛ ترتیب نمایش می‌تواند متفاوت باشد."
            />
        </flux:radio.group>

        <flux:error name="selection_mode" />
    </flux:field>

    <!-- Standard select menu ... -->
    <flux:select wire:model.live="standard_id" variant="listbox" placeholder="استانداردی انتخاب کنید ..."
                 label="استاندارد" searchable class="mb-3">
        @foreach ($standards as $standard)
            <flux:select.option value="{{$standard->id}}">{{ $standard->name_fa }}</flux:select.option>
        @endforeach
    </flux:select>


    @if ($exam_type === \App\Enums\ExamType::Quiz->value)
        <flux:checkbox.group
            wire:model.live="selected_chapters"
            variant="cards"
            class="mb-4 flex-col"
            wire:key="chapters-{{ $standard_id }}"
        >
            <div
                wire:loading
                wire:target="standard_id"
                class="text-amber-500 dark:text-amber-300"
            >
                <flux:icon.loading />
            </div>

            @if ($standard_id && $chapters->isEmpty())
                <flux:text>
                    فصلی برای این استاندارد تعریف نشده است.
                </flux:text>
            @endif

            @foreach ($chapters as $chapter)
                @if ($chapter->eligible_questions_count > 0)
                    <flux:checkbox
                        value="{{ $chapter->id }}"
                        label="{{ $chapter->number . ' - ' . $chapter->title }}"
                        description="تعداد سؤال‌های قابل استفاده: {{ $chapter->eligible_questions_count }}"
                        class="cursor-pointer"
                    />
                @else
                    <div
                        class="rounded-lg border border-dashed border-gray-300
                   bg-gray-100 px-3 py-2 opacity-70
                   dark:border-zinc-700 dark:bg-zinc-800/60"
                    >
                        <flux:text class="text-gray-500 dark:text-gray-400">
                            {{ $chapter->number . ' - ' . $chapter->title }}
                        </flux:text>

                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                            <flux:icon name="minus-circle" variant="mini" class="inline" />
                            بدون سؤال
                        </flux:text>
                    </div>
                @endif
            @endforeach
        </flux:checkbox.group>
    @endif

    <flux:input
        wire:model="title"
        label="عنوان آزمون"
        placeholder="مثلاً آزمون کلاسی فصل اول"
        required
    />
    @error('title')
    <flux:error>{{ $message }}</flux:error>
    @enderror

    <flux:input
        wire:model="question_count"
        type="number"
        min="1"
        label="تعداد سؤال"
        placeholder="تعداد کل سؤال‌های آزمون"
        required
    />

    <flux:input
        wire:model="seconds_per_question"
        type="number"
        min="1"
        label="زمان پاسخ‌گویی به هر سؤال (ثانیه)"
        required
    />

    <flux:input
        wire:model="pass_percentage"
        type="number"
        min="0"
        max="100"
        label="درصد قبولی"
        placeholder="اختیاری"
        clearable
    />

    <flux:textarea
        wire:model="description"
        label="توضیحات"
        placeholder="توضیحات اختیاری آزمون"
        rows="3"
    />

    <flux:badge color="orange">
        {{ 'تعداد کل سؤال‌های انتخاب شده: ' . $this->totalQuestions }}
    </flux:badge>
    @if ($exam_type !== \App\Enums\ExamType::Simulated->value)
        <flux:datetime-picker
            wire:model="starts_at"
            label="زمان شروع آزمون"
            locale="fa-IR"
            time="input"
            with-today
            selectable-header
        />

        <flux:datetime-picker
            wire:model="ends_at"
            label="زمان پایان آزمون"
            locale="fa-IR"
            time="input"
            with-today
            selectable-header
        />
    @endif

    @if ($errors->any())
        <div class="space-y-1">
            @foreach ($errors->all() as $error)
                <flux:text class="text-red-600 dark:text-red-400">
                    {{ $error }}
                </flux:text>
            @endforeach
        </div>
    @endif


    <flux:button type="submit" variant="primary" class="w-full">
        ایجاد آزمون
    </flux:button>
</form>
