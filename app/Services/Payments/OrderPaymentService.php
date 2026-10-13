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

            if (! $order || $order->user_id !== $user->id) {
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


            $existingPayment = Payment::query()
                ->where('order_id', $order->id)
                ->where('status', Payment::STATUS_PENDING)
                ->lockForUpdate()
                ->first();


            if ($existingPayment) {
                if (filled($existingPayment->gateway_token)) {
                    throw ValidationException::withMessages([
                        'payment' => 'برای این سفارش یک پرداخت در انتظار وجود دارد. ابتدا وضعیت پرداخت قبلی باید مشخص شود.',
                    ]);
                }

                if ($existingPayment->created_at->lte(now()->subMinutes(2))) {
                    throw ValidationException::withMessages([
                        'payment' => 'آماده‌سازی پرداخت قبلی بیش از حد طول کشیده است. ابتدا باید وضعیت آن بررسی شود.',
                    ]);
                }

                throw ValidationException::withMessages([
                    'payment' => 'پرداخت قبلی در حال آماده‌سازی است. لطفاً کمی بعد دوباره تلاش کنید.',
                ]);
            }



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
