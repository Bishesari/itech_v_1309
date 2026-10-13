<?php

namespace App\Services\Products;

use App\Enums\ProductType;
use App\Models\Product;
use App\Models\Standard;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function syncStandards(Product $product, array $standardIds): void
    {
        $standardIds = array_values(array_unique(array_map('intval', $standardIds)));

        if ($product->type === ProductType::WrittenSampleQuestion) {
            if (count($standardIds) !== 1) {
                throw ValidationException::withMessages([
                    'standard_ids' => 'محصول بانک سؤال باید دقیقاً به یک استاندارد متصل باشد.',
                ]);
            }

            $standard = Standard::query()
                ->whereKey($standardIds[0])
                ->where('is_active', true)
                ->first();

            if (! $standard) {
                throw ValidationException::withMessages([
                    'standard_ids' => 'استاندارد انتخاب‌شده معتبر یا فعال نیست.',
                ]);
            }
        }

        DB::transaction(function () use ($product, $standardIds) {
            $product->standards()->sync($standardIds);
        });
    }
}
