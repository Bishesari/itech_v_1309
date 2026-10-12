<?php

use App\Http\Controllers\SepPaymentController;
use App\Services\Authorization\CurrentRoleContextService;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');

Route::middleware('auth')->group(function () {
    // صفحه انتخاب نقش (برای کاربر چندنقشه وقتی context ندارد)
    Route::livewire('/role-selection', 'pages::auth.select-context')->name('role-selection');

    Route::get('/payments/sep-test', [
        SepPaymentController::class,
        'show',
    ])->name('payments.sep-test');

    Route::get('/payments/sep/callback', [
        SepPaymentController::class,
        'callback',
    ])->name('payments.sep.callback');

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




