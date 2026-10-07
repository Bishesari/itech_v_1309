{{-- View: resources/views/livewire/auth/forgot-password.blade.php --}}
<div class="flex flex-col gap-6 pb-5">
    {{-- Header --}}
    <div class="mb-1 text-center">
        <flux:heading size="xl">{{__('فراموشی کلمه عبور')}}</flux:heading>

        @if ($step === 2)
            <flux:subheading class="mt-2">
                @if (!$timer)
                    {{__('شماره موبایل خود را انتخاب کنید تا کد تأیید ارسال شود.')}}
                @else
                    {{__('کد تأیید ارسال شده را وارد کنید.')}}
                @endif
            </flux:subheading>
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
                    autofocus
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
                    wire:loading.attr="disabled"
                    wire:target="checkIdentity"
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
            @if (count($mobiles) > 1 && !$expiresAt)


                <flux:field>
                    <flux:label class="text-xs font-light!">
                        <span>{{__('شماره موبایل')}}</span>
                    </flux:label>
                    <flux:select
                        wire:model="selectedMobileId"
                        variant="listbox"
                        placeholder="انتخاب موبایل"
                    >
                        @foreach($mobiles as $mobile)
                            <flux:select.option value="{{ $mobile['id'] }}">
                                {{ \App\Support\MobileFormatter::mask($mobile['mobile']) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:error name="selectedMobileId" />

                </flux:field>

                @if ($errorMessage)
                    <flux:callout variant="danger" icon="exclamation-triangle">
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
                    <span wire:loading.remove wire:target="selectMobile">
                        {{__('ارسال کد تأیید')}}
                    </span>
                    <span wire:loading wire:target="selectMobile">
                        {{__('در حال پردازش...')}}
                        <flux:icon.loading class="size-4 inline" />
                    </span>
                </flux:button>

            @else
                {{-- Selected mobile display --}}
                @if ($selectedMobileId)
                    @php
                        $selectedMobile = collect($mobiles)->firstWhere('id', $selectedMobileId);
                    @endphp

                    @if ($selectedMobile)
                        <div class="text-center">
                            <flux:text>{{__('کد تأیید به شماره زیر ارسال شد:')}}</flux:text>
                            <flux:heading class="mt-1">
                                {{ \App\Support\MobileFormatter::mask($selectedMobile['mobile']) }}
                            </flux:heading>
                        </div>
                    @endif
                @endif

                {{-- OTP Form --}}
                <form wire:submit="verifyOtp" class="space-y-6">
                    <flux:field>
                        <flux:label class="text-center">{{__('کد تأیید')}}</flux:label>

                        <flux:otp
                            wire:model="otp"
                            id="otp-input-wrapper"
                            :error:icon="false"
                            error:class="text-center"
                            class="mx-auto"
                            dir="ltr"
                            inputmode="numeric"
                            x-data
                            x-init="
                                $wire.on('focus-otp', () => {
                                    $nextTick(() => {
                                        const firstInput = $el.querySelector('input');
                                        if (firstInput) {
                                            firstInput.focus();
                                            firstInput.select();
                                        }
                                    });
                                });
                            "
                        >
                            <flux:otp.input />
                            <flux:otp.input />
                            <flux:otp.input />
                            <flux:otp.separator />
                            <flux:otp.input />
                            <flux:otp.input />
                            <flux:otp.input />
                        </flux:otp>
                    </flux:field>

                    {{-- Timer --}}
                    <div
                        x-data="{
                            interval: null,

                            get timer() {
                                return $wire.timer;
                            },

                            start() {
                                clearInterval(this.interval);

                                if (this.timer <= 0) return;

                                this.interval = setInterval(() => {
                                    $wire.decrementTimer();
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

                            $wire.on('start-timer', () => {
                                $nextTick(() => start());
                            });

                            $wire.on('stop-timer', () => {
                                clearInterval(interval);
                            });

                            $watch('timer', (value) => {
                                if (value <= 0) {
                                    clearInterval(interval);
                                }
                            });
                        "
                        class="text-center"
                    >
                        <template x-if="timer > 0">
                            <flux:text>
                                زمان باقی‌مانده:
                                <span class="font-medium tabular-nums" dir="ltr" x-text="formatTime()"></span>
                            </flux:text>
                        </template>

                        <template x-if="timer <= 0">
                            <div class="space-y-3">
                                <flux:text class="text-red-600">
                                    {{__('کد تأیید منقضی شده است.')}}
                                </flux:text>
                                <flux:button
                                    type="button"
                                    variant="ghost"
                                    wire:click="resendOtp"
                                    wire:loading.attr="disabled"
                                    wire:target="resendOtp"
                                    class="w-full"
                                    :loading="false"
                                >
                                    <span wire:loading.remove wire:target="resendOtp">
                                        {{__('ارسال مجدد کد')}}
                                    </span>
                                    <span wire:loading wire:target="resendOtp">
                                        {{__('در حال ارسال...')}}
                                        <flux:icon.loading class="size-4 inline" />
                                    </span>
                                </flux:button>
                            </div>
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
                        x-bind:disabled="timer <= 0"
                    >
                        <span wire:loading.remove wire:target="verifyOtp">
                            {{__('تأیید و بازیابی کلمه عبور')}}
                        </span>
                        <span wire:loading wire:target="verifyOtp">
                            {{__('در حال بررسی...')}}
                            <flux:icon.loading class="size-4 inline" />
                        </span>
                    </flux:button>
                </form>
            @endif

            {{-- Back button --}}
            <div class="text-center">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="resetAll"
                    wire:loading.attr="disabled"
                    wire:target="resetAll"
                >
                    {{__('بازگشت')}}
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Login link --}}
    <div class="mt-2 text-center">
        <flux:link href="{{ route('login') }}" wire:navigate>
            {{__('بازگشت به صفحه ورود')}}
        </flux:link>
    </div>
</div>
