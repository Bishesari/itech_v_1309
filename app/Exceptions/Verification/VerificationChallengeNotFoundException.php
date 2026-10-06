<?php

declare(strict_types=1);

namespace App\Exceptions\Verification;

use Exception;

final class VerificationChallengeNotFoundException extends Exception
{
    protected $message = 'Active verification challenge not found.';
}
