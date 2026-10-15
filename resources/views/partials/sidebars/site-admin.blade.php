<flux:badge color="{{$context->role->color}}" class="py-2 mb-1">{{$context->role->name}}</flux:badge>

<flux:sidebar.nav>

    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                       x-data="{ loading: false }"
                       @click="loading = true"
    >
        <span>{{ __('داشبرد') }}</span>
        <flux:icon.loading x-show="loading" class="inline absolute left-2 top-2 size-3.5 text-stone-500"/>
    </flux:sidebar.item>

    <flux:sidebar.group icon="home" expandable heading="{{__('اطلاعات پایه')}}" class="grid"
                        :expanded="request()->routeIs(['role.index', 'province.index', 'branch.index', 'field.index', 'standard.index', 'user.index'])"
    >
        <flux:sidebar.item icon="user-group" :href="route('field.index')" :current="request()->routeIs('field.index')"
                           wire:navigate x-data="{ loading: false }"  @click="loading = true">
            <span>{{ __('رشته های آموزشی') }}</span>
            <flux:icon.loading x-show="loading" class="inline absolute left-1.5 top-2 size-3.5 text-purple-500"/>
        </flux:sidebar.item>
        <flux:sidebar.item href="#">Android app</flux:sidebar.item>
        <flux:sidebar.item href="#">Brand guidelines</flux:sidebar.item>
    </flux:sidebar.group>



    <flux:sidebar.item icon="inbox" badge="12" href="#">Inbox</flux:sidebar.item>
    <flux:sidebar.item icon="document-text" href="#">Documents</flux:sidebar.item>
    <flux:sidebar.item icon="calendar" href="#">Calendar</flux:sidebar.item>
</flux:sidebar.nav>

