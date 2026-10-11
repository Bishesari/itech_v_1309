
<flux:sidebar.group :heading="__('حساب کاربری')" class="grid">
    <flux:sidebar.item
        icon="home"
        :href="route('dashboard')"
        :current="request()->routeIs('dashboard')"
        wire:navigate
    >
        {{ __('داشبورد') }}
    </flux:sidebar.item>

    <flux:sidebar.item
        icon="user"
        :href="route('profile.edit')"
        :current="request()->routeIs('profile.edit')"
        wire:navigate
    >
        {{ __('تنظیمات حساب') }}
    </flux:sidebar.item>
</flux:sidebar.group>
