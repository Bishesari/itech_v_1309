<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Enums\VerificationPurpose;
use App\Exceptions\Verification\VerificationChallengeNotFoundException;
use App\Models\Mobile;
use App\Models\Person;
use App\Models\User;
use App\Models\VerificationChallenge;
use App\Services\Verification\VerificationChallengeService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class RegistrationService
{
    public function __construct(
        private VerificationChallengeService $verificationChallengeService,
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
            /** @var VerificationChallenge|null $lockedChallenge */
            $lockedChallenge = VerificationChallenge::query()
                ->whereKey($challenge->id)
                ->lockForUpdate()
                ->first();

            if (! $lockedChallenge || $lockedChallenge->verified_at !== null) {
                throw new VerificationChallengeNotFoundException;
            }

            $person = Person::create([
                'nationality_type' => $lockedChallenge->nationality_type,
                'identity' => $lockedChallenge->identity,
                'first_name_fa' => $lockedChallenge->first_name_fa,
                'last_name_fa' => $lockedChallenge->last_name_fa,
            ]);

            $mobileModel = Mobile::query()->firstOrCreate([
                'mobile' => $lockedChallenge->mobile,
            ]);

            $person->mobiles()->syncWithoutDetaching([$mobileModel->id]);

            $user = User::create([
                'person_id' => $person->id,
                'username' => $lockedChallenge->identity,
                'password' => $lockedChallenge->identity,
            ]);
            $lockedChallenge->update([
                'verified_at' => now(),
            ]);

            return $user;
        });
    }
}
