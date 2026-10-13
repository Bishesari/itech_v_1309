<?php

namespace Database\Seeders;

use App\Models\ProductStandard;
use Illuminate\Database\Seeder;

class ProductStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductStandard::query()->create([
            'product_id' => 1,
            'standard_id' => 1,
        ]);

        ProductStandard::query()->create([
            'product_id' => 2,
            'standard_id' => 2,
        ]);
    }
}
