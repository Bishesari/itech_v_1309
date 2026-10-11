<?php

namespace App\Http\Middleware;

use App\Services\Authorization\CurrentRoleContextService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->person) {
            return $next($request);
        }

        $service = app(CurrentRoleContextService::class);
        $person = $user->person;

        $available = $service->available($person);
        $count = $available->count();

        // کاربر بدون نقش: newcomer
        if ($count === 0) {
            $service->clear();

            return $next($request);
        }

        // فقط یک نقش معتبر: انتخاب خودکار
        if ($count === 1) {
            $only = $available->first();
            $current = $service->current($person);

            if (! $current || $current->assignment->id !== $only->id) {
                $service->select($person, $only->id);
            }

            return $next($request);
        }

        // چند نقش معتبر: انتخاب باید متعلق به همین مجموعه باشد
        $current = $service->current($person);

        $hasValidSelection = $current
            && $available->contains(
                fn ($assignment) => $assignment->id === $current->assignment->id
            );

        if (! $hasValidSelection) {
            $service->clear();

            if (! $request->routeIs('role-selection')) {
                return redirect()->route('role-selection');
            }
        }

        return $next($request);
    }
}
