<?php

Route::middleware('auth')->group(function () {
    Route::livewire('basic-info/fields', 'pages::basic-info.field.index')->name('field.index');
    Route::livewire('basic-info/standards', 'pages::basic-info.standard.index')->name('standard.index');
    Route::livewire('basic-info/standard/{standard}/chapters', 'pages::basic-info.chapter.index')->name('chapter.index');

    Route::livewire('questions', 'pages::question.index')->name('question.index');
    Route::livewire('questions/create', 'pages::question.create')->name('question.create');
    Route::livewire('questions/{question}', 'pages::question.show')->name('question.show')->middleware('auth');
    Route::livewire('questions/{question}/edit', 'pages::question.edit')->name('question.edit')->middleware('auth');
});
