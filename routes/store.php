<?php

Route::livewire('/store', 'pages::store.index')->name('store.index');
Route::livewire(
    'store/written-sample-questions/{product:slug}',
    'pages::store.written-sample-questions.show'
)->name('store.written-sample-questions.show');
Route::middleware('auth')->group(function () {
    Route::livewire('basic-info/products', 'pages::basic-info.product.index')->name('product.index');

    Route::livewire('basic-info/products/create', 'pages::basic-info.product.create')->name('product.create');

    Route::livewire('basic-info/products/{product}/edit', 'pages::basic-info.product.edit')->name('product.edit');
});
