<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run()
    {
        // Ambil kategori_id berdasarkan kode kategori
        $kategoriElektronik = DB::table('m_kategori')->where('kategori_kode', 'KTG01')->first()->kategori_id;
        $kategoriPakaian = DB::table('m_kategori')->where('kategori_kode', 'KTG02')->first()->kategori_id;
        $kategoriMakanan = DB::table('m_kategori')->where('kategori_kode', 'KTG03')->first()->kategori_id;
        $kategoriMinuman = DB::table('m_kategori')->where('kategori_kode', 'KTG04')->first()->kategori_id;
        $kategoriPerlengkapanRumah = DB::table('m_kategori')->where('kategori_kode', 'KTG05')->first()->kategori_id;

        $data = [
            // Barang untuk kategori Elektronik (KTG01)
            [
                'kategori_id' => $kategoriElektronik,
                'barang_kode' => 'BRG001',
                'barang_nama' => 'Laptop XYZ',
                'harga_beli' => 8000000,
                'harga_jual' => 9000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriElektronik,
                'barang_kode' => 'BRG002',
                'barang_nama' => 'Smartphone ABC',
                'harga_beli' => 3000000,
                'harga_jual' => 3500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriElektronik,
                'barang_kode' => 'BRG003',
                'barang_nama' => 'Tablet DEF',
                'harga_beli' => 2000000,
                'harga_jual' => 2500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriElektronik,
                'barang_kode' => 'BRG004',
                'barang_nama' => 'Kamera GHI',
                'harga_beli' => 4000000,
                'harga_jual' => 4500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriElektronik,
                'barang_kode' => 'BRG005',
                'barang_nama' => 'Printer JKL',
                'harga_beli' => 1500000,
                'harga_jual' => 1800000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Barang untuk kategori Pakaian (KTG02)
            [
                'kategori_id' => $kategoriPakaian,
                'barang_kode' => 'BRG006',
                'barang_nama' => 'Kaos Polos',
                'harga_beli' => 50000,
                'harga_jual' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPakaian,
                'barang_kode' => 'BRG007',
                'barang_nama' => 'Celana Jeans',
                'harga_beli' => 100000,
                'harga_jual' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPakaian,
                'barang_kode' => 'BRG008',
                'barang_nama' => 'Jaket Kulit',
                'harga_beli' => 150000,
                'harga_jual' => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPakaian,
                'barang_kode' => 'BRG009',
                'barang_nama' => 'Sepatu Lari',
                'harga_beli' => 200000,
                'harga_jual' => 250000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPakaian,
                'barang_kode' => 'BRG010',
                'barang_nama' => 'Topi Baseball',
                'harga_beli' => 30000,
                'harga_jual' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Barang untuk kategori Makanan (KTG03)
            [
                'kategori_id' => $kategoriMakanan,
                'barang_kode' => 'BRG011',
                'barang_nama' => 'Nasi Goreng Spesial',
                'harga_beli' => 10000,
                'harga_jual' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMakanan,
                'barang_kode' => 'BRG012',
                'barang_nama' => 'Mie Ayam Bakso',
                'harga_beli' => 8000,
                'harga_jual' => 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMakanan,
                'barang_kode' => 'BRG013',
                'barang_nama' => 'Bakso Jumbo',
                'harga_beli' => 12000,
                'harga_jual' => 18000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMakanan,
                'barang_kode' => 'BRG014',
                'barang_nama' => 'Soto Ayam',
                'harga_beli' => 15000,
                'harga_jual' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMakanan,
                'barang_kode' => 'BRG015',
                'barang_nama' => 'Gado-gado Komplit',
                'harga_beli' => 9000,
                'harga_jual' => 13000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Barang untuk kategori Minuman (KTG04)
            [
                'kategori_id' => $kategoriMinuman,
                'barang_kode' => 'BRG016',
                'barang_nama' => 'Es Teh Manis',
                'harga_beli' => 3000,
                'harga_jual' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMinuman,
                'barang_kode' => 'BRG017',
                'barang_nama' => 'Jus Jeruk',
                'harga_beli' => 7000,
                'harga_jual' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMinuman,
                'barang_kode' => 'BRG018',
                'barang_nama' => 'Kopi Hitam',
                'harga_beli' => 5000,
                'harga_jual' => 8000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMinuman,
                'barang_kode' => 'BRG019',
                'barang_nama' => 'Air Mineral',
                'harga_beli' => 2000,
                'harga_jual' => 3000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriMinuman,
                'barang_kode' => 'BRG020',
                'barang_nama' => 'Soda Gembira',
                'harga_beli' => 10000,
                'harga_jual' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Barang untuk kategori Perlengkapan Rumah (KTG05)
            [
                'kategori_id' => $kategoriPerlengkapanRumah,
                'barang_kode' => 'BRG021',
                'barang_nama' => 'Panci Stainless',
                'harga_beli' => 50000,
                'harga_jual' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPerlengkapanRumah,
                'barang_kode' => 'BRG022',
                'barang_nama' => 'Wajan Anti Lengket',
                'harga_beli' => 80000,
                'harga_jual' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPerlengkapanRumah,
                'barang_kode' => 'BRG023',
                'barang_nama' => 'Blender Listrik',
                'harga_beli' => 150000,
                'harga_jual' => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPerlengkapanRumah,
                'barang_kode' => 'BRG024',
                'barang_nama' => 'Set Peralatan Makan',
                'harga_beli' => 120000,
                'harga_jual' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => $kategoriPerlengkapanRumah,
                'barang_kode' => 'BRG025',
                'barang_nama' => 'Tempat Sampah',
                'harga_beli' => 30000,
                'harga_jual' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('m_barang')->insert($data);
    }
}