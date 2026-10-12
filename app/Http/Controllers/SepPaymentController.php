<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\SepPaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

            return redirect()
                ->route('payments.sep-test')
                ->with('sep_token_received', true)
                ->with('sep_res_num', $payment->res_num);
        } catch (Throwable $exception) {
            report($exception);

            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);

            return redirect()
                ->route('payments.sep-test')
                ->withErrors([
                    'payment' => 'دریافت توکن از درگاه ناموفق بود. گزارش خطا ثبت شد.',
                ]);
        }
    }

    public function callback(Request $request): View
    {
        // دریافت callback به‌تنهایی تأیید پرداخت نیست.
        // بررسی تراکنش در مرحله بعد پیاده‌سازی می‌شود.

        return view('payments.sep-result', [
            'callbackReceived' => true,
            'paymentStatus' => 'pending',
        ]);
    }
}
