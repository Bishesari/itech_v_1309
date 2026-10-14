<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderPaymentService
{
    public function prepare(User $user, int $orderId): Payment
    {
        return DB::transaction(function () use ($user, $orderId) {
            $order = Order::query()
                ->whereKey($orderId)
                ->lockForUpdate()
                ->first();

            if (! $order || (int) $order->user_id !== (int) $user->id) {
                throw ValidationException::withMessages([
                    'order' => 'سفارش موردنظر یافت نشد.',
                ]);
            }

            if ($order->status !== 'pending') {
                throw ValidationException::withMessages([
                    'order' => 'این سفارش در وضعیت قابل پرداخت نیست.',
                ]);
            }

            $amount = (int) $order->total_amount;

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'order' => 'مبلغ سفارش معتبر نیست.',
                ]);
            }

            if ($order->items()->doesntExist()) {
                throw ValidationException::withMessages([
                    'order' => 'سفارش فاقد اقلام قابل پرداخت است.',
                ]);
            }

            // قفل و بررسی پرداخت‌های قبلی سفارش
            $payments = Payment::query()
                ->where('order_id', $order->id)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->get();

            // اگر قبلاً پرداخت موفقی ثبت شده، پرداخت جدید ممنوع است.
            if ($payments->contains(
                fn (Payment $payment) => $payment->status === Payment::STATUS_PAID
            )) {
                throw ValidationException::withMessages([
                    'payment' => 'برای این سفارش پرداخت موفق ثبت شده است؛ وضعیت سفارش باید بررسی شود.',
                ]);
            }

            // پرداخت در انتظار را بازیابی می‌کنیم؛ توکن جدید نمی‌سازیم.
            $pendingPayment = $payments->first(
                fn (Payment $payment) => $payment->status
                    === Payment::STATUS_PENDING
            );

            if ($pendingPayment) {
                if ((int) $pendingPayment->amount !== $amount) {
                    throw ValidationException::withMessages([
                        'payment' => 'مبلغ پرداخت قبلی با مبلغ سفارش مطابقت ندارد؛ ابتدا باید بررسی شود.',
                    ]);
                }

                return $pendingPayment;
            }

            // اگر پرداخت در انتظار نداریم، فقط یک پرداخت جدید می‌سازیم.
            return Payment::query()->create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $amount,
                'status' => Payment::STATUS_PENDING,
                'gateway' => 'sep',
                'res_num' => 'SEP'
                    .now()->format('ymdHis')
                    .Str::upper(Str::random(8)),
            ]);
        }, attempts: 3);
    }
}
