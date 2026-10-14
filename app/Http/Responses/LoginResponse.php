<?php

namespace App\Http\Responses;

use App\Services\Authorization\CurrentRoleContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function __construct(
        private readonly CurrentRoleContextService $roleContextService,
    ) {}

    public function toResponse($request): RedirectResponse
    {
        /** @var Request $request */
        $person = $request->user()->person;

        $contexts = $this->roleContextService->available($person);

        // اگر بیش از یک نقش فعال وجود دارد، ابتدا انتخاب نقش
        if ($contexts->count() > 1) {
            return redirect()->route('role-selection');
        }

        // اگر دقیقاً یک نقش فعال وجود دارد، آن را انتخاب کن
        if ($contexts->count() === 1) {
            $this->roleContextService->select(
                $person,
                (int) $contexts->first()->id,
            );
        }

        // مقصد بازگشت فقط باید یک مسیر داخلی معتبر باشد
        $redirect = $request->input('redirect');

        if (is_string($redirect) && str_starts_with($redirect, '/')) {
            $path = parse_url($redirect, PHP_URL_PATH);

            if (
                is_string($path)
                && ! str_starts_with($redirect, '//')
                && ! str_starts_with($path, '/\\')
            ) {
                return redirect()->to($redirect);
            }
        }

        return redirect()->intended('/dashboard');
    }
}
