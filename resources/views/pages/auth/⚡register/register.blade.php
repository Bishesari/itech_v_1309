<div
    class="flex flex-col gap-6"
    x-data="{
        fingerprint: $wire.entangle('fingerprint'),
        nationalityType: $wire.entangle('nationality_type'),

        async init() {
            this.fingerprint = await window.getFingerprint();
        },

        get identityLabel() {
            return this.nationalityType == 1
                ? 'کد ملی'
                : 'شناسه اختصاصی';
        },

        get identityMaxLength() {
            return this.nationalityType == 1 ? 10 : 20;
        }
    }"
>
    <x-auth-header
        :title="__('ثبت نام')"
        :description="__('اطلاعات خواسته شده را کامل کنید.')"
    />

    <x-auth-session-status
        class="text-center"
        :status="session('status')"
    />

    <form wire:submit="continueRegister" class="flex flex-col gap-6" autocomplete="off">

        {{-- نام --}}
        <x-my.flt_lbl
            name="first_name_fa"
            label="{{ __('نام') }}"
            required
            autofocus
            :attributes="$attributes->merge(['maxlength' => 30, 'inputmode' => 'text'])"
            class="text-lg!"
        />

        {{-- نام خانوادگی --}}
        <x-my.flt_lbl
            name="last_name_fa"
            label="{{ __('نام خانوادگی') }}"
            required
            :attributes="$attributes->merge(['maxlength' => 40, 'inputmode' => 'text'])"
            class="tracking-widest text-lg!"
        />

        {{-- نوع تابعیت --}}
        <flux:radio.group
            x-model="nationalityType"
            variant="cards"
            class="max-sm:flex-col"
        >
            @foreach (\App\Enums\NationalityType::cases() as $type)
                <flux:radio
                    value="{{ $type->value }}"
                    label="{{ $type->label() }}"
                    class="cursor-pointer"
                />
            @endforeach
        </flux:radio.group>

        <flux:error
            name="nationality_type"
            class="-mt-5! text-xs font-light!"
        />

        {{-- شناسه --}}
        <x-my.flt_lbl
            name="identity"
            alpine-label="identityLabel"
            type="text"
            inputmode="numeric"
            dir="ltr"
            required
            x-bind:maxlength="identityMaxLength"
            autocomplete="off"
            class="tracking-widest font-semibold text-lg!"
        />

        {{-- موبایل --}}
        <x-my.flt_lbl
            name="mobile"
            label="{{ __('شماره موبایل') }}"
            type="text"
            required
            dir="ltr"
            :attributes="$attributes->merge(['maxlength' => 11, 'inputmode' => 'numeric'])"
            class="tracking-widest font-semibold text-lg!"
        />

        <flux:error
            name="verification"
            class="text-center text-xs font-light!"
        />

        {{-- Submit --}}
        <div class="flex items-center justify-end">
            <flux:button
                type="submit"
                variant="primary"
                color="teal"
                class="w-full cursor-pointer"
                data-test="register-user-button"
                wire:loading.attr="disabled"
                wire:target="continueRegister"
                :loading="false"
            >

                <span wire:loading.remove wire:target="continueRegister">
                    {{ __('ادامه ثبت نام') }}
                </span>

                <span
                    wire:loading
                    wire:target="continueRegister"
                >
                    {{ __('منتظر بمانید، در حال ارسال پیامک ') }}
                    <flux:icon.loading class="size-4 inline" />
                </span>

            </flux:button>
        </div>
    </form>

    {{-- Login navigation --}}
    <div
        x-data="{ loading: false }"
        x-on:livewire:navigate.window="loading = true"
        x-on:livewire:navigated.window="loading = false"
        x-on:livewire:navigate-error.window="loading = false"
        class="text-center text-sm text-zinc-600 dark:text-zinc-400"
    >
        <span>{{ __('حساب کاربری داشته اید؟') }}</span>

        <flux:link
            :href="route('login')"
            wire:navigate
            class="mr-2"
            @click="loading = true"
        >
            <span x-show="!loading">
                {{ __('وارد شوید.') }}
            </span>

            <flux:icon.loading
                x-show="loading"
                class="inline size-4 text-yellow-500"
            />
        </flux:link>
    </div>

    {{-- OTP VERIFY Modal --}}
    <flux:modal
        name="verify-otp"
        class="md:w-96"
        :dismissible="false"
        focusable
    >
        <form
            wire:submit="verifyOtp"
            class="space-y-8" autocomplete="off"
        >
            <div class="max-w-72 mx-auto space-y-2">
                <flux:heading
                    size="lg"
                    class="text-center"
                >
                    {{ __('تایید کد پیامکی') }}
                </flux:heading>

                <flux:text class="text-center">
                    {{ __('کد پیامک شده را وارد کنید.') }}
                </flux:text>
            </div>

            <flux:otp
                wire:model="otp"
                id="otp-input-wrapper"
                submit="auto"
                :error:icon="false"
                error:class="text-center"
                class="mx-auto"
                dir="ltr"
                autocomplete="off"
            >
                <flux:otp.input autofocus />
                <flux:otp.input />
                <flux:otp.input />

                <flux:otp.separator />

                <flux:otp.input />
                <flux:otp.input />
                <flux:otp.input />
            </flux:otp>

            {{-- OTP countdown --}}
            <div
                x-data="{
                    expiresAt: $wire.entangle('otp_expires_at'),

                    remaining: 0,
                    timer: null,

                    init() {
                        this.$watch('expiresAt', () => this.start());

                        if (this.expiresAt) {
                            this.start();
                        }
                    },

                    start() {
                        clearInterval(this.timer);

                        this.update();

                        this.timer = setInterval(() => {
                            this.update();
                        }, 1000);
                    },

                    update() {
                        if (!this.expiresAt) {
                            this.remaining = 0;
                            clearInterval(this.timer);
                            return;
                        }

                        this.remaining = Math.max(
                            0,
                            Math.ceil(
                                (
                                    new Date(this.expiresAt).getTime()
                                    - Date.now()
                                ) / 1000
                            )
                        );

                        if (this.remaining === 0) {
                            clearInterval(this.timer);
                        }
                    },

                    get minutes() {
                        return String(
                            Math.floor(this.remaining / 60)
                        ).padStart(2, '0');
                    },

                    get seconds() {
                        return String(
                            this.remaining % 60
                        ).padStart(2, '0');
                    },

                    destroy() {
                        clearInterval(this.timer);
                    }
                }"
            >
                <div class="space-y-4">
                    <flux:button
                        type="submit"
                        variant="primary"
                        color="teal"
                        class="w-full cursor-pointer"
                        wire:loading.attr="disabled"
                        wire:target="verifyOtp"
                        :loading="false"
                    >
                        <span wire:loading.remove wire:target="verifyOtp">
                            {{ __('تأیید کد') }}
                        </span>
                        <span wire:loading wire:target="verifyOtp">
                            {{ __('در حال بررسی ...') }}
                             <flux:icon.loading class="size-4 inline" />
                        </span>
                    </flux:button>

                    {{-- Countdown --}}
                    <template x-if="remaining > 0">
                        <flux:button
                            type="button"
                            class="w-full"
                            disabled
                        >
                            <span
                                dir="ltr"
                                class="tabular-nums"
                                x-text="`${minutes} : ${seconds}`"
                            ></span>

                            {{ __(' تا ارسال مجدد') }}
                        </flux:button>
                    </template>

                    {{-- Resend --}}
                    <template x-if="remaining === 0">
                        <flux:button
                            type="button"
                            wire:click="resendOtp"
                            variant="primary"
                            color="teal"
                            class="w-full cursor-pointer"
                            wire:loading.attr="disabled"
                            wire:target="resendOtp"
                        >
                            <span
                                wire:loading.remove
                                wire:target="resendOtp"
                            >
                                {{ __('ارسال مجدد کد') }}
                            </span>

                            <span
                                wire:loading
                                wire:target="resendOtp"
                            >
                                {{ __('در حال ارسال ...') }}
                            </span>
                        </flux:button>
                    </template>

                    <flux:error
                        name="otp"
                        class="text-center text-xs font-light!"
                    />
                </div>
            </div>
        </form>
    </flux:modal>
</div>
