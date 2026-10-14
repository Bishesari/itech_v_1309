<div>
    <flux:heading size="lg" level="1">
        {{__('اطلاعات پایه')}}
    </flux:heading>

    <div class="inline-flex mt-2 mb-4">
        <flux:text>{{__('رشته های آموزشی')}}</flux:text>

        {{-- Create Component --}}
        <livewire:pages::basic-info.field.create />
    </div>
    <flux:separator variant="subtle"/>


</div>
