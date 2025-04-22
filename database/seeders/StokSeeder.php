<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StokSeeder extends Seeder
{
    public function run()
    {
        $barangIds = DB::table('m_barang')->pluck('barang_id')->toArray();
        $userIds = DB::table('m_user')->pluck('user_id')->toArray();
        $supplierIds = DB::table('m_supplier')->pluck('supplier_id')->toArray(); // Ambil semua supplier_id

        $data = [];

        for ($i = 0; $i < 15; $i++) {
            $data[] = [
                'barang_id'     => $barangIds[array_rand($barangIds)],
                'user_id'       => $userIds[array_rand($userIds)],
                'supplier_id'   => $supplierIds[array_rand($supplierIds)], // Masukkan ke dalam data stok
                'stok_tanggal'  => Carbon::now()->subDays(rand(0, 30)), // Acak tanggal 0-30 hari lalu
                'stok_jumlah'   => rand(10, 100),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        DB::table('t_stok')->insert($data);
    }
}
