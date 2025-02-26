<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [
            [
                'user_id' => 1,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX001',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX002',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX003',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX004',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX005',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX006',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX007',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX008',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX009',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX010',
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('t_penjualan')->insert($data);
    }
}
