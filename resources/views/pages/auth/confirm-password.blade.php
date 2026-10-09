<x-layouts::auth :title="__('تأیید رمز عبور')">
    <div class="flex flex-col gap-6 pb-5">
        <x-auth-header
            :title="__('تأیید رمز عبور')"
            :description="__('این یک بخش امن از برنامه است. لطفاً پیش از ادامه، رمز عبور خود را تأیید کنید.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        {{-- نکته: اطمینان حاصل کنید نام اتریبیوت‌ها (label/loading-label) مطابق مستندات Flux شماست --}}
        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('تأیید با کلید عبور')"
            :loading-label="__('در حال تأیید...')"
            :separator="__('یا تأیید با رمز عبور')"
        />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6"
              x-data="{ loading: false }"
              @submit="loading = true"
        >
            @csrf

            <flux:input
                name="password"
                type="password"
                :label="__('رمز عبور')"
                required
                autocomplete="off"
                maxlength="25"
                viewable
                input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                dir="ltr"
            />

            {{-- Submit --}}
            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full cursor-pointer"
                             data-test="confirm-password-button" color="violet" x-bind:disabled="loading" :loading="false">
                    <span x-show="!loading">
                        {{ __('تأیید') }}
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <span>{{ __('منتظر بمانید، در حال پردازش  ... !') }}</span>
                        <flux:icon.loading class="size-5 animate-spin" />
                    </span>
                </flux:button>
            </div>

        </form>
    </div>
</x-layouts::auth>
