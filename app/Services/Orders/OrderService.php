<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createForProduct(User $user, int $productId): Order
    {
        return DB::transaction(function () use ($user, $productId) {
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

            $price = $prices->first();
            $amount = (int) $price->amount;

            $metadata = [];

            if ($product->type === 'standard_question_bank') {
                $standards = $product->standards;

                if ($standards->count() !== 1) {
                    throw ValidationException::withMessages([
                        'product' => 'این بانک سؤالات باید دقیقاً به یک استاندارد متصل باشد.',
                    ]);
                }

                $standard = $standards->first();

                $metadata['standard_id'] = $standard->id;
                $metadata['standard_code'] = $standard->code;
                $metadata['standard_name'] = $standard->name_fa;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD'
                    . now()->format('YmdHis')
                    . strtoupper(\Illuminate\Support\Str::random(8)),
                'status' => 'pending',
                'total_amount' => $amount,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_title' => $product->title,
                'product_type' => $product->type,
                'quantity' => 1,
                'unit_amount' => $amount,
                'total_amount' => $amount,
                'metadata' => $metadata ?: null,
            ]);

            return $order->load('items');
        });
    }
}
