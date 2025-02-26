<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $data = [
            
            ['penjualan_id' => 1, 'barang_id' => 1, 'harga' => 9000000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 1, 'barang_id' => 2, 'harga' => 3500000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 1, 'barang_id' => 3, 'harga' => 2500000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 2, 'barang_id' => 4, 'harga' => 4500000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 2, 'barang_id' => 5, 'harga' => 1800000, 'jumlah' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 2, 'barang_id' => 6, 'harga' => 75000, 'jumlah' => 5, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 3, 'barang_id' => 7, 'harga' => 150000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 3, 'barang_id' => 8, 'harga' => 200000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 3, 'barang_id' => 9, 'harga' => 250000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 4, 'barang_id' => 10, 'harga' => 50000, 'jumlah' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 4, 'barang_id' => 11, 'harga' => 15000, 'jumlah' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 4, 'barang_id' => 12, 'harga' => 12000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 5, 'barang_id' => 13, 'harga' => 18000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 5, 'barang_id' => 14, 'harga' => 20000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 5, 'barang_id' => 15, 'harga' => 25000, 'jumlah' => 3, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 6, 'barang_id' => 1, 'harga' => 9000000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 6, 'barang_id' => 4, 'harga' => 4500000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 6, 'barang_id' => 7, 'harga' => 150000, 'jumlah' => 3, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 7, 'barang_id' => 10, 'harga' => 50000, 'jumlah' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 7, 'barang_id' => 13, 'harga' => 18000, 'jumlah' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 7, 'barang_id' => 1, 'harga' => 9000000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 8, 'barang_id' => 2, 'harga' => 3500000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 8, 'barang_id' => 5, 'harga' => 1800000, 'jumlah' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 8, 'barang_id' => 8, 'harga' => 200000, 'jumlah' => 4, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 9, 'barang_id' => 11, 'harga' => 15000, 'jumlah' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 9, 'barang_id' => 14, 'harga' => 20000, 'jumlah' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 9, 'barang_id' => 3, 'harga' => 2500000, 'jumlah' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            ['penjualan_id' => 10, 'barang_id' => 6, 'harga' => 75000, 'jumlah' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 10, 'barang_id' => 9, 'harga' => 250000, 'jumlah' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['penjualan_id' => 10, 'barang_id' => 12, 'harga' => 12000, 'jumlah' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('t_penjualan_detail')->insert($data);
    }
}
