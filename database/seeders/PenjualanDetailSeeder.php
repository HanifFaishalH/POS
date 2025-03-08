<?php

// database/seeders/PenjualanDetailSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PenjualanDetailSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Ambil penjualan_id yang valid dari tabel t_penjualan
        $penjualanIds = DB::table('t_penjualan')->pluck('penjualan_id')->toArray();

        // Ambil barang_id yang valid dari tabel m_barang
        $barangIds = DB::table('m_barang')->pluck('barang_id')->toArray();

        $data = [];
        foreach ($penjualanIds as $penjualanId) {
            // Setiap transaksi penjualan memiliki 1-3 item barang
            $jumlahItem = rand(1, 3);
            for ($i = 0; $i < $jumlahItem; $i++) {
                $barangId = $barangIds[array_rand($barangIds)];
                $barang = DB::table('m_barang')->where('barang_id', $barangId)->first();

                $data[] = [
                    'penjualan_id' => $penjualanId,
                    'barang_id' => $barangId,
                    'harga' => $barang->harga_jual, // Ambil harga jual dari tabel m_barang
                    'jumlah' => rand(1, 5), // Jumlah barang acak antara 1 dan 5
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('t_penjualan_detail')->insert($data);
    }
}