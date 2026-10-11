<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([ProvinceSeeder::class]);
        $this->call([CitySeeder::class]);
        $this->call([BranchSeeder::class]);
        $this->call(PersonSeeder::class);
        $this->call(MobileSeeder::class);
        $this->call(MobilePersonSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(MembershipSeeder::class);
        $this->call(RoleAssignmentSeeder::class);
    }
}
