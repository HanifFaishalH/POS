<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class BarangController extends Controller
{
    // GET /barang
    public function index()
    {
        return response()->json(BarangModel::with('kategori')->get());
    }

    // POST /barang
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|max:10',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|integer',
            'harga_jual' => 'required|integer',
            'barang_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Simpan gambar jika ada
        $fotoName = null;
        if ($request->hasFile('barang_foto')) {
            $foto = $request->file('barang_foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $targetDir = public_path('img/barang');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            $foto->move($targetDir, $fotoName);
        }
    
        // Simpan ke database
        $barang = BarangModel::create([
            'kategori_id' => $request->kategori_id,
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'barang_foto' => $fotoName,
        ]);
    
        return response()->json([
            'success' => true,
            'data' => $barang,
            'foto_url' => $fotoName ? asset('img/barang/' . $fotoName) : asset('img/barang/default.png')
        ]);
    }
    

    // GET /barang/{barang}
    public function show($id)
    {
        $barang = BarangModel::with('kategori')->find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }
        return response()->json($barang);
    }

    // PUT /barang/{barang}
    public function update(Request $request, BarangModel $barang)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|exists:m_kategori,kategori_id',
            'barang_kode' => 'sometimes|required|unique:m_barang,barang_kode,' . $barang->barang_id . ',barang_id',
            'barang_nama' => 'sometimes|required|string|max:100',
            'harga_beli' => 'sometimes|required|numeric|min:0',
            'harga_jual' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $barang->update($request->all());

        return response()->json($barang->load('kategori'), 200);
    }

    // DELETE /barang/{barang}
    public function destroy(BarangModel $barang)
    {
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus.'
        ]);
    }
}
