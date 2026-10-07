<div class="flex flex-col gap-6 pb-5">
    {{-- Header --}}

    <div class="mb-1 text-center">

        <flux:heading size="xl">{{__('فراموشی کلمه عبور')}}</flux:heading>

        @if ($step === 1)
            <flux:subheading class="mt-2">{{__('کد ملی خود را وارد کنید تا مراحل بازیابی کلمه عبور آغاز شود.')}}</flux:subheading>
        @else
            <flux:subheading class="mt-2">{{__('شماره موبایل خود را انتخاب کنید تا کد تأیید ارسال شود.')}}</flux:subheading>
        @endif
    </div>

    {{-- Step 1: Identity --}}
    @if ($step === 1)

        <form wire:submit="checkIdentity" class="flex flex-col gap-6" autocomplete="off">

            {{-- شناسه --}}
            <flux:field>
                <flux:label class="text-xs font-light!">
                    <span>{{__('کدملی یا شناسه اختصاصی')}}</span>
                </flux:label>

                <flux:input
                    wire:model="identity"
                    type="text"
                    inputmode="numeric"
                    dir="ltr"
                    required
                    input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                    maxlength="20"
                />

                <flux:error
                    name="identity"
                    class="-mt-1! text-xs font-light!"
                />
            </flux:field>

            <div class="flex items-center justify-end">
                <flux:button
                    type="submit"
                    variant="filled"
                    color="fuchsia"
                    class="w-full cursor-pointer"
                    data-test="register-user-button"
                    wire:loading.attr="disabled"
                    wire:target="continueRegister"
                    :loading="false"
                >
                    <span wire:loading.remove wire:target="checkIdentity">
                        {{ __('ادامه') }}
                    </span>
                    <span wire:loading wire:target="checkIdentity">
                        {{__('در حال بررسی...')}}
                        <flux:icon.loading class="size-4 inline" />
                    </span>

                </flux:button>
            </div>
        </form>

    @endif


    {{-- Step 2: Mobile + OTP --}}
    @if ($step === 2)

        <div class="space-y-6">

            {{-- Mobile selection --}}
            @if (count($mobiles) > 1 && ! $timer)

                <flux:field>
                    <flux:label>{{__('شماره موبایل')}}</flux:label>

                    <flux:select wire:model="selectedMobileId" variant="listbox" placeholder="انتخاب موبایل" clearable>
                        @foreach($mobiles as $mobile)
                            <flux:select.option value="{{ $mobile['id'] }}">
                                {{ \App\Support\MobileFormatter::mask($mobile['mobile']) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="selectedMobileId" />
                </flux:field>

                @if ($errorMessage)
                    <flux:callout
                        variant="danger"
                        icon="exclamation-triangle"
                    >
                        {{ $errorMessage }}
                    </flux:callout>
                @endif

                <flux:button
                    type="button"
                    variant="primary"
                    class="w-full cursor-pointer"
                    wire:click="selectMobile"
                    wire:loading.attr="disabled"
                    wire:target="selectMobile"
                    :loading="false"
                >
                    <span wire:loading.remove wire:target="selectMobile">{{__('ارسال کد تأیید')}}</span>

                    <span wire:loading wire:target="selectMobile">
                        {{__('در حال پردازش...')}}
                        <flux:icon.loading class="size-4 inline" />
                    </span>
                </flux:button>

            @else

                {{-- Selected mobile --}}
                @if ($selectedMobileId)
                    @php
                        $selectedMobile = collect($mobiles)
                            ->firstWhere('id', $selectedMobileId);
                    @endphp

                    @if ($selectedMobile)
                        <div class="text-center">
                            <flux:text>{{__('کد تأیید به شماره زیر ارسال شد:')}}</flux:text>

                            <flux:heading class="mt-1">
                                {{ \App\Support\MobileFormatter::mask($selectedMobile['mobile'] ) }}
                            </flux:heading>
                        </div>
                    @endif
                @endif


                {{-- OTP --}}
                <form wire:submit="verifyOtp" class="space-y-6">
                    <flux:field>
                        <flux:label class="text-center">{{__('کد تأیید')}}</flux:label>

                        <flux:otp
                            wire:model="otp"
                            length="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            class="mx-auto"
                        />

                        <flux:error name="otp" />
                    </flux:field>


                    {{-- OTP timer --}}
                    <div
                        x-data="{
                            timer: @entangle('timer'),
                            interval: null,

                            start() {
                                clearInterval(this.interval);

                                if (this.timer <= 0) {
                                    return;
                                }

                                this.interval = setInterval(() => {
                                    if (this.timer > 0) {
                                        this.timer--;
                                    } else {
                                        clearInterval(this.interval);
                                    }
                                }, 1000);
                            },

                            formatTime() {
                                const minutes = Math.floor(this.timer / 60);
                                const seconds = this.timer % 60;

                                return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                            }
                        }"
                        x-init="
                            start();

                            $wire.on('set_timer', () => {
                                timer = $wire.timer;
                                start();
                            });

                            $wire.on('stop_timer', () => {
                                clearInterval(interval);
                                timer = 0;
                            });
                        "
                        class="text-center"
                    >
                        <template x-if="timer > 0">
                            <flux:text>
                                زمان باقی‌مانده:
                                <span
                                    class="font-medium tabular-nums"
                                    dir="ltr"
                                    x-text="formatTime()"
                                ></span>
                            </flux:text>
                        </template>

                        <template x-if="timer <= 0">
                            <flux:text>{{__('کد تأیید منقضی شده است.')}}</flux:text>
                        </template>
                    </div>


                    @if ($errorMessage)
                        <flux:callout variant="danger" icon="exclamation-triangle">
                            {{ $errorMessage }}
                        </flux:callout>
                    @endif


                    <flux:button
                        type="submit"
                        variant="primary"
                        class="w-full cursor-pointer"
                        wire:loading.attr="disabled"
                        wire:target="verifyOtp"
                        :loading="false"
                    >
                        <span wire:loading.remove wire:target="verifyOtp">{{__('تأیید و بازیابی کلمه عبور')}}</span>
                        <span wire:loading wire:target="verifyOtp">
                            {{__('در حال بررسی...')}}
                            <flux:icon.loading class="size-4 inline" />
                        </span>
                    </flux:button>

                </form>

            @endif


            {{-- Back --}}
            <div class="text-center">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="resetAll"
                    wire:loading.attr="disabled"
                    wire:target="resetAll"
                >
                    بازگشت
                </flux:button>
            </div>

        </div>

    @endif

    {{-- Login link --}}
    <div class="mt-2 text-center">
        <flux:link href="{{ route('login') }}" wire:navigate>{{__('بازگشت به صفحه ورود')}}</flux:link>
    </div>
</div>
