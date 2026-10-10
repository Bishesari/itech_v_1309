<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
    <form method="POST" wire:submit="deleteUser" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('آیا از حذف حساب کاربری خود اطمینان دارید؟') }}</flux:heading>

            <flux:subheading>
                {{ __('با حذف حساب کاربری، تمامی اطلاعات و داده‌های آن به‌طور دائم پاک خواهند شد. لطفاً برای تأیید نهایی حذف حساب، رمز عبور خود را وارد کنید.') }}
            </flux:subheading>
        </div>



        <flux:input wire:model="password" :label="__('رمز عبور')" type="password" viewable autocomplete="off"
                    input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                    dir="ltr"
        />

        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('انصراف') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="danger" type="submit" data-test="confirm-delete-user-button">
                {{ __('حذف حساب کاربری') }}
            </flux:button>
        </div>
    </form>
</flux:modal>

