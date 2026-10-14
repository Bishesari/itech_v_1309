<?php

namespace App\Services\Payments;

use App\Enums\ProductType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductAccess;
use Illuminate\Support\Facades\DB;

class CompleteOrderPaymentService
{
    /**
     * ثبت نتیجه پرداختی که قبلاً نزد SEP تأیید شده است.
     *
     * این متد نباید قبل از تأیید واقعی SEP فراخوانی شود.
     */
    public function complete(int $paymentId, string $refNum): string
    {
        return DB::transaction(function () use ($paymentId, $refNum) {
            $payment = Payment::query()
                ->whereKey($paymentId)
                ->lockForUpdate()
                ->first();

            if (! $payment || $payment->gateway !== 'sep') {
                return 'failed';
            }

            // Callback تکراری برای پرداخت موفق
            if ($payment->isPaid()) {
                return $payment->gateway_reference === $refNum
                    ? 'paid'
                    : 'pending';
            }

            if ($payment->status !== Payment::STATUS_PENDING) {
                return 'failed';
            }

            $order = Order::query()
                ->whereKey($payment->order_id)
                ->lockForUpdate()
                ->first();

            if (! $order || $order->user_id !== $payment->user_id) {
                return 'pending';
            }

            if ($order->status !== 'pending') {
                return 'pending';
            }

            // مبلغ پرداخت باید با مبلغ نهایی سفارش برابر باشد.
            if (
                (int) $payment->amount <= 0
                || (int) $payment->amount !== (int) $order->total_amount
            ) {
                return 'pending';
            }

            $items = $order->items()
                ->lockForUpdate()
                ->get();

            if (
                $items->isEmpty()
                || (int) $items->sum('total_amount')
                !== (int) $order->total_amount
            ) {
                return 'pending';
            }

            // شماره مرجع نباید به پرداخت دیگری تعلق داشته باشد.
            $referenceAlreadyUsed = Payment::query()
                ->where('gateway_reference', $refNum)
                ->where('id', '!=', $payment->id)
                ->exists();

            if ($referenceAlreadyUsed) {
                return 'pending';
            }

            // ابتدا وضعیت پرداخت و سفارش را در همین تراکنش ثبت می‌کنیم.
            $payment->update([
                'status' => Payment::STATUS_PAID,
                'gateway_reference' => $refNum,
                'verified_at' => now(),
            ]);

            $order->update([
                'status' => 'paid',
            ]);

            // صدور دسترسی برای بانک سؤال؛ فقط یک بار برای هر قلم سفارش.
            foreach ($items as $item) {
                if ($item->product_type !== ProductType::WrittenSampleQuestion->value) {
                    continue;
                }

                if (! $item->product_id) {
                    // خطا باعث Rollback تمام تغییرات این تراکنش می‌شود.
                    throw new \RuntimeException(
                        'Question bank order item has no product ID.'
                    );
                }

                ProductAccess::query()->firstOrCreate(
                    ['order_item_id' => $item->id],
                    [
                        'user_id' => $order->user_id,
                        'product_id' => $item->product_id,
                        'granted_at' => now(),
                    ],
                );
            }

            return 'paid';
        }, attempts: 3);
    }
}
