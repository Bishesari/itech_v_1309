<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Contracts\SmsGateway;

final class FakeSmsGateway implements SmsGateway
{
    public function sendOtp(
        string $mobile,
        string $verificationCode,
    ): void {
        logger()->info('OTP', [
            'mobile' => $mobile,
            'code' => $verificationCode,
        ]);
    }

    public function sendPassword(
        string $mobile,
        string $password,
    ): void {
        logger()->info('Password SMS', [
            'mobile' => $mobile,
            'password' => $password,
        ]);
    }
}
