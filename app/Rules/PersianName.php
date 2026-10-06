<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class PersianName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // چون قبلاً نرمال‌سازی شده، فقط کاراکترهای مجاز را بررسی می‌کنیم.
        if (! is_string($value) || ! preg_match('/^[آ-یء\s\x{200C}]+$/u', $value)) {
            $fail('فیلد :attribute باید فقط شامل حروف فارسی باشد.');
        }
    }
}
