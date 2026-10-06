<?php

declare(strict_types=1);

namespace App\Exceptions\Verification;

use Exception;

final class ActiveVerificationChallengeException extends Exception
{
    protected $message = 'Verification challenge is still active.';
}
