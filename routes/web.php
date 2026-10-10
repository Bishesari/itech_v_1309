<?php

use App\Services\Authorization\CurrentRoleContextService;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');

Route::middleware('auth')->group(function () {
    // صفحه انتخاب نقش (برای کاربر چندنقشه وقتی context ندارد)
    Route::livewire('/role-selection', 'auth::select-context')->name('role-selection');

    Route::middleware('role.context')->group(function () {
        Route::get('dashboard', function (CurrentRoleContextService $contextService) {
            $person = request()->user()->person;
            $context = $contextService->current($person);

            return view('dashboard', compact('context'));
        })->name('dashboard');



    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';




