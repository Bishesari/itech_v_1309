<flux:badge color="{{$context->role->color}}" class="py-2 mb-1 ">{{$context->role->name}}</flux:badge>

<flux:sidebar.nav>

    <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                       x-data="{ loading: false }"
                       @click="loading = true"
    >
        <span>{{ __('داشبرد') }}</span>
        <flux:icon.loading x-show="loading" class="inline absolute left-2 top-2 size-3.5 text-stone-500"/>
    </flux:sidebar.item>

    <flux:sidebar.group icon="circle-stack" expandable heading="{{__('اطلاعات پایه')}}" class="grid"
                        :expanded="request()->routeIs(['field.index', 'standard.index'])"
    >
        <flux:sidebar.item icon="academic-cap" :href="route('field.index')" :current="request()->routeIs('field.index')"
                           wire:navigate x-data="{ loading: false }"  @click="loading = true">
            <span>{{ __('رشته های آموزشی') }}</span>
            <flux:icon.loading x-show="loading" class="inline absolute left-1.5 top-2 size-3.5 text-purple-500"/>
        </flux:sidebar.item>
        <flux:sidebar.item icon="clipboard-document-list" :href="route('standard.index')" :current="request()->routeIs('standard.index')"
                           wire:navigate x-data="{ loading: false }"  @click="loading = true">
            <span>{{ __('استانداردها') }}</span>
            <flux:icon.loading x-show="building-library" class="inline absolute left-1.5 top-2 size-3.5 text-purple-500"/>
        </flux:sidebar.item>
    </flux:sidebar.group>

    <flux:sidebar.group icon="building-library" expandable heading="{{__('بانک سوالات')}}" class="grid"
                        :expanded="request()->routeIs(['question.index'])"
    >
        <flux:sidebar.item icon="question-mark-circle" :href="route('question.index')" :current="request()->routeIs('question.index')"
                           wire:navigate x-data="{ loading: false }"  @click="loading = true">
            <span>{{ __('مدیریت سوالات') }}</span>
            <flux:icon.loading x-show="loading" class="inline absolute left-1.5 top-2 size-3.5 text-purple-500"/>
        </flux:sidebar.item>
    </flux:sidebar.group>
</flux:sidebar.nav>

