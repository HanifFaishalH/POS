<?php

// database/seeders/StokSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    public function run()
    {
        // Ambil barang_id dan user_id yang valid
        $barangIds = DB::table('m_barang')->pluck('barang_id')->toArray();
        $userIds = DB::table('m_user')->pluck('user_id')->toArray();

        $data = [];
        for ($i = 0; $i < 15; $i++) {
            $data[] = [
                'barang_id' => $barangIds[array_rand($barangIds)], // Ambil barang_id secara acak
                'user_id' => $userIds[array_rand($userIds)], // Ambil user_id secara acak
                'stok_tanggal' => now(),
                'stok_jumlah' => rand(50, 300), // Jumlah stok acak antara 50 dan 300
            ];
        }

        DB::table('t_stok')->insert($data);
    }
}