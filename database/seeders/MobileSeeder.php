<?php

namespace Database\Seeders;

use App\Models\Mobile;
use Illuminate\Database\Seeder;

class MobileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mobile::query()->updateOrCreate(
            [
                'mobile' => '09177755924',
            ],
        );
        Mobile::query()->updateOrCreate(
            [
                'mobile' => '09034336111',
            ],
        );
        Mobile::query()->updateOrCreate(
            [
                'mobile' => '09350568163',
            ],
        );
        Mobile::query()->updateOrCreate(
            [
                'mobile' => '09113169302',
            ],
        );
        Mobile::query()->updateOrCreate(
            [
                'mobile' => '09177729312',
            ],
        );
    }
}
