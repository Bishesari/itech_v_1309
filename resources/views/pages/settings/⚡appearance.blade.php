<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('تنظیمات تاریک/روشن')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('تنظیمات ظاهر') }}</flux:heading>

    <x-pages::settings.layout :heading="__('ظاهر')" :subheading="__('تنظیمات تم و حالت نمایش پنل کاربری خود را تغییر دهید')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('روشن') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('تاریک') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('سیستم') }}</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>
