
@php
    use App\Enums\RoleCode;
    use App\Services\Authorization\CurrentRoleContextService;

    $user = auth()->user();
    $person = $user?->person;

    $service = app(CurrentRoleContextService::class);

    $context = $person
        ? $service->current($person)
        : null;

    $hasNoRoles = false;

    if ($person && ! $context) {
        $hasNoRoles = $service->available($person)->isEmpty();
    }
@endphp

@if ($context)
    @switch($context->role->code)
        @case(RoleCode::SiteAdmin->value)
            @include('partials.sidebars.site-admin')
            @break

        @case(RoleCode::Founder->value)
            @include('partials.sidebars.founder')
            @break

        @case(RoleCode::Administrative->value)
            @include('partials.sidebars.administrative')
            @break

        @case(RoleCode::Instructor->value)
            @include('partials.sidebars.instructor')
            @break

        @case(RoleCode::Student->value)
            @include('partials.sidebars.student')
            @break
    @endswitch
@elseif ($hasNoRoles)
    @include('partials.sidebars.newcomer')
@endif
