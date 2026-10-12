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

    Route::post('/payments/sep-test/token', [
        SepPaymentController::class,
        'requestToken',
    ])->name('payments.sep-test.token');


    Route::middleware('role.context')->group(function () {
        Route::get('dashboard', function (CurrentRoleContextService $contextService) {
            $person = request()->user()->person;
            $context = $contextService->current($person);

            return view('dashboard', compact('context'));
        })->name('dashboard');
    });
});
// این مسیر باید از سمت درگاه قابل دسترسی باشد.
Route::post(
    '/payments/sep/callback',
    [SepPaymentController::class, 'callback']
)->name('payments.sep.callback');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';




