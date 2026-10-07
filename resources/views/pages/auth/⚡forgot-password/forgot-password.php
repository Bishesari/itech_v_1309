<?php

use App\Models\Mobile;
use App\Models\User;
use App\Services\Auth\PasswordResetService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::auth')]
#[Title('فراموشی کلمه عبور')]
class extends Component
{
    public int $step = 1;

    public string $identity = '';

    public ?User $user = null;

    public array $mobiles = [];

    public ?int $selectedMobileId = null;

    public string $otp = '';

    public int $timer = 0;

    public string $errorMessage = '';

    public function checkIdentity(
        PasswordResetService $passwordResetService,
    ): void {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $this->validate([
            'identity' => ['required'],
        ]);

        $user = $passwordResetService->findByIdentity(
            $this->identity,
        );

        if (! $user) {
            $this->addError(
                'identity',
                'شناسه یافت نشد.',
            );

            return;
        }

        $mobiles = $passwordResetService->mobiles($user);

        if ($mobiles->isEmpty()) {
            $this->addError(
                'identity',
                'هیچ شماره موبایلی برای این شناسه ثبت نشده است.',
            );

            return;
        }

        $this->user = $user;

        $this->mobiles = $mobiles
            ->map(fn (Mobile $mobile) => [
                'id' => $mobile->id,
                'mobile' => $mobile->mobile,
            ])
            ->values()
            ->all();

        $this->selectedMobileId = null;
        $this->otp = '';
        $this->timer = 0;

        $this->step = 2;

        if (count($this->mobiles) === 1) {
            $this->selectedMobileId = $this->mobiles[0]['id'];

            $this->sendOtp(
                $passwordResetService,
            );
        }
    }

    public function selectMobile(
        PasswordResetService $passwordResetService,
    ): void {
        $this->resetErrorBag();
        $this->errorMessage = '';

        if (! $this->user || ! $this->selectedMobileId) {
            return;
        }

        $mobile = $this->getSelectedMobile();

        if (! $mobile) {
            $this->addError(
                'selectedMobileId',
                'شماره موبایل انتخاب‌شده معتبر نیست.',
            );

            return;
        }

        $this->sendOtp(
            $passwordResetService,
        );
    }

    public function sendOtp(
        PasswordResetService $passwordResetService,
    ): void {
        $this->resetErrorBag();
        $this->errorMessage = '';

        if (! $this->user || ! $this->selectedMobileId) {
            return;
        }

        $mobile = $this->getSelectedMobile();

        if (! $mobile) {
            $this->addError(
                'selectedMobileId',
                'شماره موبایل انتخاب‌شده معتبر نیست.',
            );

            return;
        }

        try {
            $challenge = $passwordResetService->issueVerification(
                user: $this->user,
                mobile: $mobile,
            );

            $this->otp = '';

            $this->timer = max(
                0,
                now()->diffInSeconds(
                    $challenge->expires_at,
                    false,
                ),
            );

            $this->dispatch('set_timer');
            $this->dispatch('focus-otp');
        } catch (Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function verifyOtp(
        PasswordResetService $passwordResetService,
    ): void {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        if (! $this->user || ! $this->selectedMobileId) {
            $this->errorMessage = 'اطلاعات بازیابی کلمه عبور معتبر نیست.';

            return;
        }

        $mobile = $this->getSelectedMobile();

        if (! $mobile) {
            $this->errorMessage = 'شماره موبایل انتخاب‌شده معتبر نیست.';

            return;
        }

        try {
            $passwordResetService->reset(
                user: $this->user,
                mobile: $mobile,
                verificationCode: $this->otp,
            );

            $this->dispatch('stop_timer');

            $this->redirectRoute('login');
        } catch (Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function resetAll(): void
    {
        $this->reset([
            'identity',
            'user',
            'mobiles',
            'selectedMobileId',
            'otp',
            'timer',
            'errorMessage',
        ]);

        $this->resetErrorBag();

        $this->step = 1;

        $this->dispatch('stop_timer');
    }

    private function getSelectedMobile(): ?Mobile
    {
        if (! $this->user || ! $this->selectedMobileId) {
            return null;
        }

        return $this->user->person
            ->mobiles()
            ->whereKey($this->selectedMobileId)
            ->first();
    }
};
