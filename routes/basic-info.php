<?php

Route::middleware('auth')->group(function () {
    Route::livewire('basic-info/fields', 'pages::basic-info.field.index')->name('field.index');
    Route::livewire('basic-info/standards', 'pages::basic-info.field.index')->name('field.index');
});
