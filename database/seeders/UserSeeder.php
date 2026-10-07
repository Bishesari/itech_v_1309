<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            [
                'username' => 'Yasser',
            ],
            [
                'person_id' => 1,
                'password' => '673673',
            ],
        );
        User::query()->updateOrCreate(
            [
                'username' => 'Neda',
            ],
            [
                'person_id' => 2,
                'password' => '673673',
            ],
        );
    }
}
