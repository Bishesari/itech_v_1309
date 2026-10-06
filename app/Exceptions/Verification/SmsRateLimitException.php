<?php

declare(strict_types=1);

namespace App\Exceptions\Verification;

use Exception;

final class SmsRateLimitException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'تعداد درخواست‌های ارسال پیامک بیش از حد مجاز است.'
        );
    }
}
