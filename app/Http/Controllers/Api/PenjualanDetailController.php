<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenjualanDetailModel;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function getByBarang($barang_id)
    {
        $details = PenjualanDetailModel::with(['barang', 'penjualan'])
            ->where('barang_id', $barang_id)
            ->get();

        return response()->json($details);
    }
}
