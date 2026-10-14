<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Orders\OrderService;
use App\Services\Payments\CompleteOrderPaymentService;
use App\Services\Payments\OrderPaymentService;
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
        $product = Product::query()
            ->where('slug', 'sep-payment-test')
            ->where('is_active', true)
            ->firstOrFail();

        return view('payments.sep-test', compact('product'));
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

            // خطای شبکه به‌تنهایی شکست قطعی پرداخت را ثابت نمی‌کند.
            // وضعیت پرداخت را pending نگه می‌داریم تا بررسی شود.

            return redirect()
                ->route('payments.sep-test')
                ->withErrors([
                    'payment' => 'پاسخ قطعی از درگاه دریافت نشد. وضعیت درخواست باید بررسی شود؛ لطفاً پرداخت جدیدی ایجاد نکنید.',
                ]);
        }
    }

    public function callback(
        Request $request,
        SepPaymentGateway $gateway,
        CompleteOrderPaymentService $completionService,
    ): View {
        $resNum = trim((string) $request->input('ResNum', ''));
        $refNum = trim((string) $request->input('RefNum', ''));
        $state = strtoupper(trim((string) $request->input('State', '')));
        $callbackStatus = (int) $request->input('Status', 0);

        // بررسی اولیه اطلاعات بازگشتی از درگاه
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
            // پیدا کردن پرداخت؛ در این مرحله هنوز هیچ تغییری نمی‌دهیم.
            $payment = Payment::query()
                ->where('res_num', $resNum)
                ->where('gateway', 'sep')
                ->first();

            if (! $payment) {
                return view('payments.sep-result', [
                    'callbackReceived' => true,
                    'paymentStatus' => 'failed',
                ]);
            }

            // Callback تکراری برای پرداخت تأییدشده
            if ($payment->isPaid()) {
                $status = $payment->gateway_reference === $refNum
                    ? 'paid'
                    : 'pending';

                return view('payments.sep-result', [
                    'callbackReceived' => true,
                    'paymentStatus' => $status,
                ]);
            }

            if ($payment->status !== Payment::STATUS_PENDING) {
                return view('payments.sep-result', [
                    'callbackReceived' => true,
                    'paymentStatus' => 'failed',
                ]);
            }

            // درخواست شبکه‌ای Verify خارج از تراکنش دیتابیس اجرا می‌شود.
            $verification = $gateway->verifyTransaction($refNum);
            $detail = $verification['TransactionDetail'] ?? null;

            $valid = is_array($detail)
                && (string) ($detail['RefNum'] ?? '') === $refNum
                && (int) ($detail['OrginalAmount'] ?? 0) === $payment->amount
                && (int) ($detail['AffectiveAmount'] ?? 0) === $payment->amount
                && (int) ($detail['TerminalNumber'] ?? 0)
                === (int) config('services.sep.terminal_id');

            if (! $valid) {
                return view('payments.sep-result', [
                    'callbackReceived' => true,
                    'paymentStatus' => 'pending',
                ]);
            }

            // ثبت نتیجه با قفل رکورد برای جلوگیری از پردازش هم‌زمان

            $status = $completionService->complete(
                paymentId: (int) $payment->id,
                refNum: $refNum,
            );
        } catch (Throwable $exception) {
            report($exception);

            // خطای ارتباطی یا خطای غیرمنتظره، موفقیت یا شکست قطعی نیست.
            $status = 'pending';
        }

        return view('payments.sep-result', [
            'callbackReceived' => true,
            'paymentStatus' => $status,
        ]);
    }

    public function payOrder(
        Request $request,
        Order $order,
        OrderPaymentService $paymentService,
        SepPaymentGateway $gateway,
    ): RedirectResponse {
        $payment = $paymentService->prepare(
            $request->user(),
            (int) $order->getKey(),
        );

        return $this->redirectToSep($payment, $gateway);
    }

    public function buyProduct(
        Request $request,
        Product $product,
        OrderService $orderService,
        OrderPaymentService $paymentService,
        SepPaymentGateway $gateway,
    ): RedirectResponse {
        $order = $orderService->createForProduct(
            $request->user(),
            (int) $product->getKey(),
        );

        $payment = $paymentService->prepare(
            $request->user(),
            (int) $order->getKey(),
        );

        return $this->redirectToSep($payment, $gateway);
    }

    private function redirectToSep(
        Payment $payment,
        SepPaymentGateway $gateway,
    ): RedirectResponse {
        $paymentUrl = rtrim(
            (string) config('services.sep.send_token_url'),
            '/',
        );

        try {
            if (blank($paymentUrl)) {
                throw new \RuntimeException(
                    'SEP payment URL is not configured.'
                );
            }

            // پرداخت باید همچنان در انتظار باشد.
            $payment->refresh();

            if ($payment->status !== Payment::STATUS_PENDING) {
                throw new \RuntimeException(
                    'Payment is no longer pending.'
                );
            }

            // اگر توکن قبلاً ذخیره شده، همان را استفاده می‌کنیم.
            if (filled($payment->gateway_token)) {
                return redirect()->away(
                    $paymentUrl.'?token='
                    .urlencode($payment->gateway_token),
                );
            }

            // شناسه مالکیت درخواست صدور توکن
            $requestKey = (string) Str::uuid();

            // فقط یک درخواست می‌تواند قفل را دریافت کند.
            $claimed = Payment::query()
                ->whereKey($payment->id)
                ->where('status', Payment::STATUS_PENDING)
                ->whereNull('gateway_token')
                ->whereNull('token_request_key')
                ->update([
                    'token_request_key' => $requestKey,
                ]);

            if ($claimed !== 1) {
                $payment->refresh();

                // شاید درخواست هم‌زمان توکن را ذخیره کرده باشد.
                if (
                    $payment->status === Payment::STATUS_PENDING
                    && filled($payment->gateway_token)
                ) {
                    return redirect()->away(
                        $paymentUrl.'?token='
                        .urlencode($payment->gateway_token),
                    );
                }

                return redirect()
                    ->route('dashboard')
                    ->withErrors([
                        'payment' => 'درخواست پرداخت قبلی در حال بررسی است. برای جلوگیری از پرداخت تکراری، درخواست جدیدی ارسال نشد.',
                    ]);
            }

            // درخواست شبکه‌ای خارج از تراکنش دیتابیس اجرا می‌شود.
            $token = $gateway->requestToken(
                amount: (int) $payment->amount,
                resNum: $payment->res_num,
                redirectUrl: route('payments.sep.callback'),
            );

            // توکن فقط توسط مالک قفل ثبت می‌شود.
            $updated = Payment::query()
                ->whereKey($payment->id)
                ->where('status', Payment::STATUS_PENDING)
                ->whereNull('gateway_token')
                ->where('token_request_key', $requestKey)
                ->update([
                    'gateway_token' => $token,
                    'token_request_key' => null,
                ]);

            if ($updated !== 1) {
                throw new \RuntimeException(
                    'SEP token was received but could not be saved safely.'
                );
            }

            return redirect()->away(
                $paymentUrl.'?token='.urlencode($token),
            );
        } catch (Throwable $exception) {
            report($exception);

            // عمداً وضعیت پرداخت را failed نمی‌کنیم.
            // ممکن است SEP توکن صادر کرده باشد، اما پاسخ به ما نرسیده باشد.
            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'payment' => 'آماده‌سازی پرداخت کامل نشد. برای جلوگیری از پرداخت تکراری، وضعیت درخواست باید بررسی شود.',
                ]);
        }
    }
}
