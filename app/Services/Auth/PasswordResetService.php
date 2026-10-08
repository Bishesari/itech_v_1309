<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Contracts\SmsGateway;
use App\Enums\VerificationPurpose;
use App\Models\Mobile;
use App\Models\User;
use App\Services\Verification\VerificationChallengeService;
use App\Support\PasswordGenerator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class PasswordResetService
{
    public function __construct(
        protected SmsGateway $smsGateway,
        protected VerificationChallengeService $verificationChallengeService,
    ) {}

    public function findByIdentity(string $identity): ?User
    {
        return User::query()
            ->whereHas('person', fn ($query) => $query->where('identity', $identity))
            ->with('person.mobiles')
            ->first();
    }

    public function mobiles(User $user): Collection
    {
        return $user->person->mobiles;
    }

    public function issueVerification(
        User $user,
        Mobile $mobile,
        ?string $fingerprint = null,
        ?string $ip = null,
    ) {
        $this->ensureMobileBelongsToUser($user, $mobile);

        $person = $user->person;

        return $this->verificationChallengeService->issue(
            purpose: VerificationPurpose::PasswordReset,
            firstNameFa: $person->first_name_fa,
            lastNameFa: $person->last_name_fa,
            nationalityType: $person->nationality_type,
            identity: $person->identity,
            mobile: $mobile->mobile,
            fingerprint: $fingerprint,
            ip: $ip,
        );
    }

    public function reset(
        User $user,
        Mobile $mobile,
        string $verificationCode,
    ): void {
        $this->ensureMobileBelongsToUser($user, $mobile);

        // verify باید خودش status/verified_at را هندل کند
        $this->verificationChallengeService->verify(
            purpose: VerificationPurpose::PasswordReset,
            mobile: $mobile->mobile,
            verificationCode: $verificationCode,
        );

        $password = PasswordGenerator::generate();

        // اگر ارسال شکست بخورد، پسورد تغییر نکند
        $this->smsGateway->sendPassword(
            mobile: $mobile->mobile,
            username: $user->username,
            password: $password,
        );

        DB::transaction(function () use ($user, $password): void {
            $user->update([
                'password' => $password, // hashing توسط cast مدل User
            ]);
        });
    }

    protected function ensureMobileBelongsToUser(User $user, Mobile $mobile): void
    {
        $exists = $user->person
            ->mobiles()
            ->whereKey($mobile->getKey())
            ->exists();

        if (! $exists) {
            throw new InvalidArgumentException('شماره موبایل انتخاب‌شده متعلق به این کاربر نیست.');
        }
    }
}
