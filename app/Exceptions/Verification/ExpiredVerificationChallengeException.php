<?php

declare(strict_types=1);

namespace App\Exceptions\Verification;

use RuntimeException;

final class ExpiredVerificationChallengeException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'کد تأیید منقضی شده است.'
        );
    }
}
