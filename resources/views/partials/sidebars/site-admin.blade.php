<flux:sidebar.group heading="مدیریت سیستم" class="grid">
    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
        داشبورد
    </flux:sidebar.item>
</flux:sidebar.group>
