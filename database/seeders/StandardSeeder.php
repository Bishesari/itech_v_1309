<?php

namespace Database\Seeders;

use App\Models\Standard;
use Illuminate\Database\Seeder;

class StandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standards = [
            [
                'field_id' => 1,
                'code' => '251340530000031',
                'name_fa' => 'ساخت برنامه های وب با استفاده از Laravel Framework',
                'name_en' => 'Build Web Apps Using Laravel Framework',
                'nazari_h' => 30,
                'amali_h' => 60,
                'karvarzi_h' => 0,
                'project_h' => 0,
                'sum_h' => 90,
                'required_h' => 120,
            ],

            [
                'field_id' => 1,
                'code' => '351230530000111',
                'name_fa' => 'برنامه نویسی Python',
                'name_en' => 'Programming Python',
                'nazari_h' => 20,
                'amali_h' => 90,
                'karvarzi_h' => 0,
                'project_h' => 0,
                'sum_h' => 110,
                'required_h' => 80,
            ],

            [
                'field_id' => 1,
                'code' => '732120530140001',
                'name_fa' => 'کاربر ICDL',
                'name_en' => 'ICDL USER',
                'nazari_h' => 30,
                'amali_h' => 100,
                'karvarzi_h' => 0,
                'project_h' => 0,
                'sum_h' => 130,
                'required_h' => 80,
            ],
        ];

        foreach ($standards as $data) {
            Standard::updateOrCreate(
                ['code' => $data['code']],
                [
                    'field_id' => $data['field_id'],
                    'name_fa' => $data['name_fa'],
                    'name_en' => $data['name_en'],
                    'nazari_h' => $data['nazari_h'],
                    'amali_h' => $data['amali_h'],
                    'karvarzi_h' => $data['karvarzi_h'],
                    'project_h' => $data['project_h'],
                    'sum_h' => $data['sum_h'],
                    'required_h' => $data['required_h'],
                ]
            );
        }
    }
}
