<?php

namespace Database\Seeders;

use App\Models\MobilePerson;
use Illuminate\Database\Seeder;

class MobilePersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MobilePerson::query()->updateOrCreate(
            [
                'mobile_id' => '1',
                'person_id' => '1',
            ],
        );
        MobilePerson::query()->updateOrCreate(
            [
                'mobile_id' => '2',
                'person_id' => '1',
            ],
        );
        MobilePerson::query()->updateOrCreate(
            [
                'mobile_id' => '3',
                'person_id' => '1',
            ],
        );
        MobilePerson::query()->updateOrCreate(
            [
                'mobile_id' => '4',
                'person_id' => '1',
            ],
        );
        MobilePerson::query()->updateOrCreate(
            [
                'mobile_id' => '2',
                'person_id' => '2',
            ],
        );
    }
}
