<?php

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تنظیمات پروفایل')] class extends Component {

    public string $username = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->username = Auth::user()->username ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $validated = $this->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:25',
                'alpha_dash:ascii',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('username')) {
            $user->save();

            Flux::toast(text: __('نام کاربری با موفقیت به‌روزرسانی شد.'), variant: 'success');
        } else {
            Flux::toast(text: __('تغییری در اطلاعات ایجاد نشد.'), variant: 'warning');
        }
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('تنظیمات پروفایل') }}</flux:heading>

    <x-pages::settings.layout :heading="__('پروفایل')" :subheading="__('نام کاربری خود را به‌روزرسانی کنید')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input
                wire:model="username"
                :label="__('نام کاربری')"
                autocomplete="off"
                required
                autofocus
                input:class="text-center pt-6.5 pb-5.5 tracking-widest font-semibold text-lg!"
                maxlength="25"
                type="text"
                dir="ltr"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('ذخیره') }}
                    </flux:button>
                </div>
            </div>
        </form>

        <livewire:pages::settings.delete-user-form />

    </x-pages::settings.layout>
</section>
