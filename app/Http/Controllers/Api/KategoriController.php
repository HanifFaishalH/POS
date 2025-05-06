<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    // GET /kategori
    public function index()
    {
        return response()->json(KategoriModel::withCount('barang')->get());
    }

    // POST /kategori
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kategori = KategoriModel::create($request->all());

        return response()->json($kategori, 201);
    }

    // GET /kategori/{kategori}
    public function show($id)
    {
        $kategori = KategoriModel::with('barang')->find($id);
        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json($kategori);
    }

    // PUT /kategori/{kategori}
    public function update(Request $request, KategoriModel $kategori)
    {
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'sometimes|required|unique:m_kategori,kategori_kode,' . $kategori->kategori_id . ',kategori_id',
            'kategori_nama' => 'sometimes|required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kategori->update($request->all());

        return response()->json($kategori->load(['barang'])->loadCount('barang'), 200);
    }

    // DELETE /kategori/{kategori}
    public function destroy(KategoriModel $kategori)
    {
        // Cek apakah masih ada barang terkait
        if ($kategori->barang()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih digunakan oleh barang.'
            ], 400);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}
