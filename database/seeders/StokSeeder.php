<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['barang_id' => 1, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 50,],
            ['barang_id' => 2, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 100,],
            ['barang_id' => 3, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 75,],
            ['barang_id' => 4, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 30,],
            ['barang_id' => 5, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 60,],
            ['barang_id' => 6, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 200,],
            ['barang_id' => 7, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 150,],
            ['barang_id' => 8, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 120,],
            ['barang_id' => 9, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 90,],
            ['barang_id' => 10, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 180,],
            ['barang_id' => 11, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 250,],
            ['barang_id' => 12, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 300,],
            ['barang_id' => 13, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 220,],
            ['barang_id' => 14, 'user_id' => 2, 'stok_tanggal' => now(), 'stok_jumlah' => 170,],
            ['barang_id' => 15, 'user_id' => 1, 'stok_tanggal' => now(), 'stok_jumlah' => 280,],
        ];
        DB::table('t_stok')->insert($data);
    }
}
