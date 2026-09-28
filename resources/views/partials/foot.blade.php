<flux:footer
    class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
>
    <div class="container mx-auto px-4 py-8">

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">

            {{-- About --}}
            <div>
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center"
                    wire:navigate
                >
                    <x-logo class="h-12 text-zinc-700 dark:text-zinc-300" />
                </a>

                <flux:text class="mt-4 leading-7">
                    {{ __('آموزشگاه آی‌تک؛ ارائه‌دهنده دوره‌های مهارتی و کاربردی.') }}
                </flux:text>
            </div>

            {{-- Links --}}
            <nav aria-label="{{ __('لینک‌های سایت') }}">
                <flux:heading size="sm">
                    {{ __('دسترسی سریع') }}
                </flux:heading>

                <div class="mt-4 flex flex-col gap-2">
                    <a
                        href="{{ route('home') }}"
                        wire:navigate
                        class="text-sm text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
                    >
                        {{ __('صفحه اصلی') }}
                    </a>
                </div>
            </nav>

            {{-- Contact --}}
            <address class="not-italic">
                <flux:heading size="sm">
                    {{ __('تماس با ما') }}
                </flux:heading>

                <flux:text class="mt-4 leading-7">
                    {{ __('بوشهر') }}
                </flux:text>
            </address>

        </div>

        <flux:separator class="my-6" />

        <div class="text-center">
            <flux:text size="sm">
                <span class="font-semibold">&copy;</span>
                {{ __('تمامی حقوق برای آموزشگاه آی‌تک محفوظ است.') }}
            </flux:text>
        </div>

    </div>
</flux:footer>
