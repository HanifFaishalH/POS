<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori_id' => 1,
                'barang_kode' => 'BRG001',
                'barang_nama' => 'Laptop XYZ',
                'harga_beli' => 8000000,
                'harga_jual' => 9000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1,
                'barang_kode' => 'BRG002',
                'barang_nama' => 'Smartphone ABC',
                'harga_beli' => 3000000,
                'harga_jual' => 3500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1,
                'barang_kode' => 'BRG003',
                'barang_nama' => 'Tablet DEF',
                'harga_beli' => 2000000,
                'harga_jual' => 2500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1,
                'barang_kode' => 'BRG004',
                'barang_nama' => 'Kamera GHI',
                'harga_beli' => 4000000,
                'harga_jual' => 4500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1,
                'barang_kode' => 'BRG005',
                'barang_nama' => 'Printer JKL',
                'harga_beli' => 1500000,
                'harga_jual' => 1800000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5 barang untuk kategori 2
            [
                'kategori_id' => 2,
                'barang_kode' => 'BRG006',
                'barang_nama' => 'Kaos Polos',
                'harga_beli' => 50000,
                'harga_jual' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'BRG007',
                'barang_nama' => 'Celana Jeans',
                'harga_beli' => 100000,
                'harga_jual' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'BRG008',
                'barang_nama' => 'Jaket Kulit',
                'harga_beli' => 150000,
                'harga_jual' => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'BRG009',
                'barang_nama' => 'Sepatu Lari',
                'harga_beli' => 200000,
                'harga_jual' => 250000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2,
                'barang_kode' => 'BRG010',
                'barang_nama' => 'Topi Baseball',
                'harga_beli' => 30000,
                'harga_jual' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5 barang untuk kategori 3
            [
                'kategori_id' => 3,
                'barang_kode' => 'BRG011',
                'barang_nama' => 'Nasi Goreng Spesial',
                'harga_beli' => 10000,
                'harga_jual' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'BRG012',
                'barang_nama' => 'Mie Ayam Bakso',
                'harga_beli' => 8000,
                'harga_jual' => 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'BRG013',
                'barang_nama' => 'Bakso Jumbo',
                'harga_beli' => 12000,
                'harga_jual' => 18000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'BRG014',
                'barang_nama' => 'Soto Ayam',
                'harga_beli' => 15000,
                'harga_jual' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 3,
                'barang_kode' => 'BRG015',
                'barang_nama' => 'Gado-gado Komplit',
                'harga_beli' => 9000,
                'harga_jual' => 13000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
