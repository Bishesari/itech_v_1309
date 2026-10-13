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

        $token = null;

        try {
            if (blank($paymentUrl)) {
                throw new \RuntimeException(
                    'SEP payment URL is not configured.',
                );
            }

            $token = $gateway->requestToken(
                amount: (int) $payment->amount,
                resNum: $payment->res_num,
                redirectUrl: route('payments.sep.callback'),
            );

            // فقط وقتی توکن ذخیره می‌شود که پرداخت هنوز
            // در انتظار باشد و قبلاً توکنی نداشته باشد.
            $updated = Payment::query()
                ->whereKey($payment->id)
                ->where('status', Payment::STATUS_PENDING)
                ->whereNull('gateway_token')
                ->update([
                    'gateway_token' => $token,
                ]);

            if ($updated !== 1) {
                throw new \RuntimeException(
                    'Payment state changed before the SEP token could be saved.',
                );
            }

            return redirect()->away(
                $paymentUrl.'?token='.urlencode($token),
            );
        } catch (Throwable $exception) {
            report($exception);

            // فقط اگر هنوز توکنی از SEP دریافت نشده، می‌توانیم
            // خطای آماده‌سازی را با اطمینان بیشتری ثبت کنیم.
            if ($token === null) {
                Payment::query()
                    ->whereKey($payment->id)
                    ->where('status', Payment::STATUS_PENDING)
                    ->whereNull('gateway_token')
                    ->update([
                        'status' => Payment::STATUS_FAILED,
                    ]);
            }

            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'payment' => 'آماده‌سازی پرداخت کامل نشد. وضعیت پرداخت باید بررسی شود.',
                ]);
        }
    }

}
