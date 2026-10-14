<?php

namespace App\Http\Responses;

use App\Services\Authorization\CurrentRoleContextService;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function __construct(
        private readonly CurrentRoleContextService $roleContextService,
    ) {}

    public function toResponse($request): RedirectResponse
    {
        $person = $request->user()->person;

        $redirectPath = $this->productRedirectPath(
            $request->input('redirect'),
        );

        $sessionKey = 'auth.after_role_selection_redirect';

        if ($redirectPath !== null) {
            session()->put($sessionKey, $redirectPath);
        } else {
            session()->forget($sessionKey);
        }

        $contexts = $this->roleContextService->available($person);

        // چند نقش فعال: ابتدا انتخاب نقش
        if ($contexts->count() > 1) {
            return redirect()->route('role-selection');
        }

        // یک نقش فعال: انتخاب خودکار
        if ($contexts->count() === 1) {
            $this->roleContextService->select(
                $person,
                (int) $contexts->first()->id,
            );
        }

        // مقصد مجاز را پس از ورود بازیابی کن
        $destination = session()->pull($sessionKey);

        if (is_string($destination)) {
            return redirect()->to($destination);
        }

        return redirect()->intended('/dashboard');
    }

    private function productRedirectPath(mixed $redirect): ?string
    {
        if (! is_string($redirect)) {
            return null;
        }

        $path = parse_url($redirect, PHP_URL_PATH);

        if (
            ! is_string($path)
            || ! preg_match(
                '#^/store/written-sample-questions/[a-zA-Z0-9_-]+/?$#',
                $path,
            )
        ) {
            return null;
        }

        return $path;
    }
}
