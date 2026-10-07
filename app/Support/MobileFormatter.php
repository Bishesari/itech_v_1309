<?php

declare(strict_types=1);

namespace App\Support;

final class MobileFormatter
{
    public static function mask(string $mobile): string
    {
        return substr($mobile, -3).'****'.substr($mobile, 0, 4);

    }
}
