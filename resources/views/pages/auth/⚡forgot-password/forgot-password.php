<?php

use App\Models\Mobile;
use App\Models\User;
use App\Services\Auth\PasswordResetService;
use Carbon\CarbonInterface;
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

    public ?CarbonInterface $expiresAt = null;

    public string $errorMessage = '';

    public function mount(): void
    {
        // محاسبه timer اگر expiresAt وجود داشت (برای refresh صفحه)
        if ($this->expiresAt) {
            $this->timer = max(0, now()->diffInSeconds($this->expiresAt, false));
        }
    }

    public function checkIdentity(PasswordResetService $passwordResetService): void
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $this->validate([
            'identity' => ['required', 'string', 'max:20'],
        ]);

        $user = $passwordResetService->findByIdentity($this->identity);

        if (! $user) {
            $this->addError('identity', 'شناسه یافت نشد.');

            return;
        }

        $mobiles = $passwordResetService->mobiles($user);

        if ($mobiles->isEmpty()) {
            $this->addError('identity', 'هیچ شماره موبایلی برای این شناسه ثبت نشده است.');

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
        $this->expiresAt = null;

        $this->step = 2;

        // اگر فقط یک موبایل داره، خودکار ارسال کن
        if (count($this->mobiles) === 1) {
            $this->selectedMobileId = $this->mobiles[0]['id'];
            $this->sendOtp($passwordResetService);
        }
    }

    public function selectMobile(PasswordResetService $passwordResetService): void
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $this->validate([
            'selectedMobileId' => ['required', 'integer'],
        ]);

        if (! $this->user) {
            $this->errorMessage = 'اطلاعات بازیابی معتبر نیست. لطفاً دوباره تلاش کنید.';

            return;
        }

        $mobile = $this->getSelectedMobile();

        if (! $mobile) {
            $this->addError('selectedMobileId', 'شماره موبایل انتخاب‌شده معتبر نیست.');

            return;
        }

        $this->sendOtp($passwordResetService);
    }

    public function sendOtp(PasswordResetService $passwordResetService): void
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        if (! $this->user || ! $this->selectedMobileId) {
            $this->errorMessage = 'اطلاعات بازیابی معتبر نیست.';

            return;
        }

        $mobile = $this->getSelectedMobile();

        if (! $mobile) {
            $this->errorMessage = 'شماره موبایل انتخاب‌شده معتبر نیست.';

            return;
        }

        try {
            $challenge = $passwordResetService->issueVerification(
                user: $this->user,
                mobile: $mobile,
            );

            $this->otp = '';
            $this->expiresAt = $challenge->expires_at;
            $this->timer = max(0, now()->diffInSeconds($this->expiresAt, false));

            $this->dispatch('start-timer');
            $this->dispatch('focus-otp');
        } catch (Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function resendOtp(PasswordResetService $passwordResetService): void
    {
        $this->sendOtp($passwordResetService);
    }

    public function verifyOtp(PasswordResetService $passwordResetService): void
    {
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

        // چک کردن انقضای تایمر
        if ($this->timer <= 0 || ($this->expiresAt && now()->isAfter($this->expiresAt))) {
            $this->otp = '';
            $this->errorMessage = 'کد تأیید منقضی شده است. لطفاً کد جدید درخواست کنید.';

            return;
        }

        try {
            $passwordResetService->reset(
                user: $this->user,
                mobile: $mobile,
                verificationCode: $this->otp,
            );

            $this->dispatch('stop-timer');

            session()->flash('success', 'کلمه عبور با موفقیت بازیابی شد.');

            $this->redirectRoute('login', navigate: true);
        } catch (Throwable $e) {
            $this->otp = '';
            $this->errorMessage = $e->getMessage();
        }
    }

    public function decrementTimer(): void
    {
        if ($this->timer > 0) {
            $this->timer--;
        }

        // اگر از سمت سرور هم چک کنیم
        if ($this->expiresAt && now()->isAfter($this->expiresAt)) {
            $this->timer = 0;
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
            'expiresAt',
            'errorMessage',
        ]);

        $this->resetErrorBag();
        $this->step = 1;
        $this->dispatch('stop-timer');
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
