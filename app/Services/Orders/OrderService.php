<?php

namespace App\Services\Orders;

use App\Enums\ProductType;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAccess;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createForProduct(User $user, int $productId): Order
    {
        return DB::transaction(function () use ($user, $productId) {
            // قفل محصول برای هماهنگی درخواست‌های هم‌زمان خرید
            $product = Product::query()
                ->whereKey($productId)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'product' => 'این محصول در حال حاضر قابل خرید نیست.',
                ]);
            }

            // جلوگیری از خرید مجدد بانک سؤال در صورت داشتن دسترسی فعال
            if ($product->type === ProductType::WrittenSampleQuestion) {
                $hasAccess = ProductAccess::query()
                    ->where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->whereNull('revoked_at')
                    ->exists();

                if ($hasAccess) {
                    throw ValidationException::withMessages([
                        'product' => 'شما قبلاً به این محصول دسترسی دارید.',
                    ]);
                }
            }

            $now = now();

            $prices = $product->prices()
                ->where('starts_at', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>', $now);
                })
                ->orderByDesc('starts_at')
                ->lockForUpdate()
                ->get();

            if ($prices->count() !== 1) {
                throw ValidationException::withMessages([
                    'product' => $prices->isEmpty()
                        ? 'برای این محصول قیمت معتبر ثبت نشده است.'
                        : 'برای این محصول چند قیمت معتبر وجود دارد؛ لطفاً تنظیم قیمت‌ها بررسی شود.',
                ]);
            }

            $amount = (int) $prices->first()->amount;

            if ($amount < 1) {
                throw ValidationException::withMessages([
                    'product' => 'قیمت محصول معتبر نیست.',
                ]);
            }

            $metadata = [];

            if ($product->type === ProductType::WrittenSampleQuestion) {
                $standards = $product->standards;

                if ($standards->count() !== 1) {
                    throw ValidationException::withMessages([
                        'product' => 'این بانک سؤالات باید دقیقاً به یک استاندارد متصل باشد.',
                    ]);
                }

                $standard = $product->standards()
                    ->where('is_active', true)
                    ->first();

                if (! $standard) {
                    throw ValidationException::withMessages([
                        'product' => 'استاندارد متصل به این محصول فعال یا معتبر نیست.',
                    ]);
                }

                $metadata = [
                    'standard_id' => $standard->id,
                    'standard_code' => $standard->code,
                    'standard_name' => $standard->name_fa,
                ];
            }

            // بررسی سفارش پرداخت‌نشده قبلی برای همین محصول
            $pendingOrders = Order::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereHas('items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->with(['items', 'payments'])
                ->lockForUpdate()
                ->get()
                ->filter(function (Order $order) use ($product) {
                    return $order->items->count() === 1
                        && (int) $order->items->first()->product_id
                        === (int) $product->id;
                })
                ->values();

            if ($pendingOrders->count() > 1) {
                throw ValidationException::withMessages([
                    'order' => 'چند سفارش پرداخت‌نشده برای این محصول وجود دارد؛ ابتدا باید وضعیت آن‌ها بررسی شود.',
                ]);
            }

            if ($pendingOrders->isNotEmpty()) {
                $existingOrder = $pendingOrders->first();

                // سفارش پرداخت‌شده‌ای که هنوز pending مانده،
                // وضعیت ناسازگار دارد و نباید دوباره شارژ شود.
                if (
                    $existingOrder->payments->contains(
                        fn ($payment) => $payment->status
                            === 'paid'
                    )
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'وضعیت پرداخت قبلی نیاز به بررسی دارد؛ پرداخت جدید ایجاد نشد.',
                    ]);
                }

                // فقط اگر مبلغ سفارش با قیمت فعلی یکی باشد،
                // همان سفارش را بازیابی می‌کنیم.
                if (
                    (int) $existingOrder->total_amount !== $amount
                    || (int) $existingOrder->items->first()->total_amount
                    !== $amount
                ) {
                    throw ValidationException::withMessages([
                        'order' => 'مبلغ سفارش قبلی با قیمت فعلی محصول متفاوت است؛ ابتدا باید سفارش بررسی شود.',
                    ]);
                }

                return $existingOrder;
            }

            // ایجاد سفارش جدید، فقط وقتی سفارش قابل‌بازیابی نداریم
            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => 'ORD-'.Str::ulid(),
                'status' => 'pending',
                'total_amount' => $amount,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_title' => $product->title,
                'product_type' => $product->type->value,
                'quantity' => 1,
                'unit_amount' => $amount,
                'total_amount' => $amount,
                'metadata' => $metadata ?: null,
            ]);

            return $order->load('items');
        }, attempts: 3);
    }
}
