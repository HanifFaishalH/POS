<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id'   => 1,
                'supplier_kode' => 'SPL001',
                'supplier_nama' => 'Supplier Jaya Abadi',
            ],
            [
                'supplier_id'   => 2,
                'supplier_kode' => 'SPL002',
                'supplier_nama' => 'Supplier Makmur Sentosa',
            ],
            [
                'supplier_id'   => 3,
                'supplier_kode' => 'SPL003',
                'supplier_nama' => 'Supplier Anugerah Sejahtera',
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
