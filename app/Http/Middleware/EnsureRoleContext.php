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

        // بدون نقش => newcomer => اجازه ورود به داشبورد
        if ($count === 0) {
            // اگر context قبلی مانده، پاک کن
            $service->clear();
            return $next($request);
        }

        // تک نقش => اتوماتیک انتخاب کن
        if ($count === 1) {
            $only = $available->first();
            $current = $service->current($person);

            if (! $current || $current->assignment->id !== $only->id) {
                $service->select($person, $only->id);
            }

            return $next($request);
        }

        // چند نقش => اگر current ندارد، بفرست صفحه انتخاب نقش
        $current = $service->current($person);

        if (! $current && ! $request->routeIs('role-selection')) {
            return redirect()->route('role-selection');
        }

        return $next($request);
    }
}
