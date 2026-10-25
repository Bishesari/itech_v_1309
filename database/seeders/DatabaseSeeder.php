<?php

namespace Database\Seeders;

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
        $this->call(ProvinceSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(BranchSeeder::class);

        $this->call(PersonSeeder::class);
        $this->call(MobileSeeder::class);
        $this->call(MobilePersonSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RoleAssignmentSeeder::class);

        $this->call(FieldSeeder::class);

        $this->call(StandardSeeder::class);
        //$this->call(ProductSeeder::class);
        //$this->call(ProductPriceSeeder::class);
        //$this->call(ProductStandardSeeder::class);
    }
}
