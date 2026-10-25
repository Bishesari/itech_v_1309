<?php

Route::middleware('auth')->group(function () {
    Route::livewire('exams', 'pages::exam.index')->name('exam.index');
    Route::livewire('exams/{exam}', 'pages::exam.show')->name('exam.show');
    // مدیریت آزمون‌ها
    Route::livewire('basic-info/exams', 'pages::basic-info.exam.index')->name('exam.manage.index');
});

Route::middleware('auth')->group(function () {
    // فهرست آزمون‌ها
    // Route::livewire('/exams', 'pages::exam.index')
    //     ->name('exams.index');

    // معرفی آزمون
    // Route::livewire('/exams/{exam}', 'pages::exam.show')
    //     ->name('exams.show');

    // شروع آزمون یا ادامه تلاش قبلی
    // Route::post('/exams/{exam}/start', ...)
    //     ->name('exams.start');

    // صفحه پاسخ‌گویی
    // Route::livewire('/exam-attempts/{attempt}', ...)
    //     ->name('exam-attempts.show');

    // ثبت نهایی آزمون
    // Route::post('/exam-attempts/{attempt}/submit', ...)
    //     ->name('exam-attempts.submit');
});
