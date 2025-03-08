<?php

// database/seeders/PenjualanSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PenjualanSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Ambil user_id yang valid dari tabel m_user
        $userIds = DB::table('m_user')->pluck('user_id')->toArray();

        $data = [];
        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'user_id' => $userIds[array_rand($userIds)], // Ambil user_id secara acak
                'pembeli' => $faker->name,
                'penjualan_kode' => 'TRX' . str_pad($i, 3, '0', STR_PAD_LEFT), // Generate kode penjualan
                'penjualan_tanggal' => $faker->dateTimeBetween('-1 month', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_penjualan')->insert($data);
    }
}