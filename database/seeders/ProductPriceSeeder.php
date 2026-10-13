<?php

namespace Database\Seeders;

use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product_prices = [
            [
                'product_id' => 1,
                'amount' => 110000,
                'starts_at' => '2026-10-13 00:00:00',
            ],
            [
                'product_id' => 2,
                'amount' => 120000,
                'starts_at' => '2026-10-13 00:00:00',
            ],
        ];

        foreach ($product_prices as $data) {
            ProductPrice::query()->Create([
                'product_id' => $data['product_id'],
                'amount' => $data['amount'],
                'starts_at' => $data['starts_at'],
            ]
            );
        }
    }
}
