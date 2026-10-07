<?php
Route::livewire('/register', 'pages::auth.register')->name('register');
Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('forgot-password');
Route::middleware('auth')->group(function () {
    Route::livewire('/select-role', 'pages::auth.select-context')->name('role-context.select');
});
