<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\SepPaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class SepPaymentController extends Controller
{
    private const TEST_AMOUNT = 100_000; // Rial

    public function show(): View
    {
        return view('payments.sep-test');
    }

    public function requestToken(
        Request $request,
        SepPaymentGateway $gateway,
    ): RedirectResponse {
        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'amount' => self::TEST_AMOUNT,
            'status' => Payment::STATUS_PENDING,
            'gateway' => 'sep',
            'res_num' => 'SEP'
                .now()->format('ymdHis')
                .Str::upper(Str::random(8)),
        ]);

        try {
            $token = $gateway->requestToken(
                amount: $payment->amount,
                resNum: $payment->res_num,
                redirectUrl: route('payments.sep.callback'),
            );

            $payment->update([
                'gateway_token' => $token,
            ]);

            $paymentUrl = rtrim(
                config('services.sep.send_token_url'),
                '/',
            ).'?token='.urlencode($token);

            return redirect()->away($paymentUrl);
        } catch (Throwable $exception) {
            report($exception);

            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);

            return redirect()
                ->route('payments.sep-test')
                ->withErrors([
                    'payment' => 'آماده‌سازی پرداخت ناموفق بود. لطفاً گزارش خطا را بررسی کنید.',
                ]);
        }
    }

    public function callback(
        Request $request,
        SepPaymentGateway $gateway,
    ): View {
        $resNum = trim((string) $request->input('ResNum', ''));
        $refNum = trim((string) $request->input('RefNum', ''));
        $state = strtoupper(
            trim((string) $request->input('State', ''))
        );

        $callbackStatus = (int) $request->input('Status', 0);
        if (
            $resNum === ''
            || $refNum === ''
            || $state !== 'OK'
            || $callbackStatus !== 2
        ) {
            return view('payments.sep-result', [
                'callbackReceived' => true,
                'paymentStatus' => 'failed',
            ]);
        }


        try {
            $status = DB::transaction(function () use (
                $resNum,
                $refNum,
                $gateway,
            ): string {
                $payment = Payment::query()
                    ->where('res_num', $resNum)
                    ->where('gateway', 'sep')
                    ->lockForUpdate()
                    ->first();

                if (! $payment) {
                    return 'failed';
                }

                // Callback تکراری نباید پرداخت را دوباره تأیید کند.
                if ($payment->isPaid()) {
                    return $payment->gateway_reference === $refNum
                        ? 'paid'
                        : 'pending';
                }

                if ($payment->status !== Payment::STATUS_PENDING) {
                    return 'failed';
                }

                $verification = $gateway->verifyTransaction($refNum);

                $detail = $verification['TransactionDetail'] ?? null;

                $valid = ($verification['Success'] ?? false) === true
                    && (int) ($verification['ResultCode'] ?? -1) === 0
                    && is_array($detail)
                    && (string) ($detail['RefNum'] ?? '') === $refNum
                    && (int) ($detail['OrginalAmount'] ?? 0) === $payment->amount
                    && (int) ($detail['AffectiveAmount'] ?? 0) === $payment->amount
                    && (int) ($detail['TerminalNumber'] ?? 0)
                    === (int) config('services.sep.terminal_id');

                if (! $valid) {
                    // پاسخ نامعتبر یا غیرمنتظره لزوماً به معنی شکست قطعی پرداخت نیست.
                    return 'pending';
                }



                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'gateway_reference' => $refNum,
                    'verified_at' => now(),
                ]);

                return 'paid';
            });
        } catch (Throwable $exception) {
            report($exception);

            // در صورت خطای ارتباط با SEP، پرداخت را خودکار failed نمی‌کنیم؛
            // ممکن است تراکنش انجام شده باشد و نیاز به بررسی مجدد داشته باشد.
            $status = 'pending';
        }

        return view('payments.sep-result', [
            'callbackReceived' => true,
            'paymentStatus' => $status,
        ]);
    }
}
