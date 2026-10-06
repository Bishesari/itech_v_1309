<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Enums\VerificationPurpose;
use App\Models\Mobile;
use App\Models\Person;
use App\Models\User;
use App\Services\Verification\VerificationChallengeService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class RegistrationService
{
    public function __construct(
        private readonly VerificationChallengeService $verificationChallengeService,
    ) {}

    public function complete(
        VerificationPurpose $purpose,
        string $mobile,
        string $verificationCode,
    ): User {
        if ($purpose !== VerificationPurpose::Registration) {
            throw new InvalidArgumentException(
                'Registration service requires registration verification purpose.'
            );
        }

        /*
         * OTP verification must happen outside the registration
         * transaction so failed-attempt increments are not rolled back.
         */
        $challenge = $this->verificationChallengeService->verify(
            purpose: $purpose,
            mobile: $mobile,
            verificationCode: $verificationCode,
        );

        return DB::transaction(function () use ($challenge): User {
            $person = Person::create([
                'nationality_type' => $challenge->nationality_type,
                'identity' => $challenge->identity,
                'first_name_fa' => $challenge->first_name_fa,
                'last_name_fa' => $challenge->last_name_fa,
            ]);

            $mobileModel = Mobile::query()->firstOrCreate([
                'mobile' => $challenge->mobile,
            ]);

            $person->mobiles()->attach($mobileModel);

            $user = User::create([
                'person_id' => $person->id,
                'username' => $challenge->identity,
                'password' => $challenge->identity,
            ]);

            $challenge->update([
                'verified_at' => now(),
            ]);

            return $user;
        });
    }
}
