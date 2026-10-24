<?php

namespace App\Enums;

enum ExamType: string
{
    case Quiz = 'quiz';
    case Midterm = 'midterm';
    case Simulated = 'simulated';

    public function label(): string
    {
        return match ($this) {
            self::Quiz => 'آزمون کلاسی',
            self::Midterm => 'آزمون میان‌ترم',
            self::Simulated => 'آزمون شبیه‌سازی‌شده',
        };
    }
}
