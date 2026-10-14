<?php

Route::middleware('auth')->group(function () {
    Route::livewire('basic-info/fields', 'pages::basic-info.field.index')->name('field.index');
});
