<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'بانک سؤالات استاندارد Laravel',
                'slug' => 'laravel-standard-question-bank',
                'type' => 'standard_question_bank',
                'short_description' => 'نمونه سؤالات استاندارد ساخت برنامه‌های وب با Laravel',
                'description' => null,
                'seo_title' => 'بانک سؤالات استاندارد Laravel',
                'seo_description' => 'نمونه سؤالات استاندارد Laravel برای آمادگی آزمون',
                'is_active' => true,
            ],
            [
                'title' => 'بانک سؤالات استاندارد Python',
                'slug' => 'python-standard-question-bank',
                'type' => 'standard_question_bank',
                'short_description' => 'نمونه سؤالات استاندارد ساخت برنامه‌نویسی python',
                'description' => null,
                'seo_title' => 'بانک سؤالات استاندارد Python',
                'seo_description' => 'نمونه سؤالات استاندارد Python برای آمادگی آزمون',
                'is_active' => true,
            ],
        ];

        foreach ($products as $data) {
            Product::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'type' => $data['type'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'seo_title' => $data['seo_title'],
                    'seo_description' => $data['seo_description'],
                    'is_active' => $data['is_active'],
                ]
            );
        }
    }
}
