<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    // Menampilkan halaman utama kategori
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem',
        ];

        $activeMenu = 'kategori';

        return view('kategori.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    // Mengambil data kategori dalam bentuk JSON untuk DataTables
    public function list(Request $request)
    {
        $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        // Filter berdasarkan kategori_kode
        if ($request->kategori_kode) {
            $kategoris->where('kategori_kode', 'like', '%' . $request->kategori_kode . '%');
        }
        
        return DataTables::of($kategoris)
            ->addIndexColumn() // Menambahkan kolom index / no urut
            ->addColumn('aksi', function ($kategori) { // Menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // Memberitahu bahwa kolom aksi berisi HTML
            ->make(true);
    }

    // Menampilkan form tambah kategori (AJAX)
    public function create_ajax()
    {
        return view('kategori.create_ajax');
    }

    // Menyimpan data kategori baru (AJAX)
    public function store_ajax(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|max:10|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100',
        ]);

        $kategori = KategoriModel::create([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data kategori berhasil disimpan',
            'data' => $kategori
        ]);
    }

    // Menampilkan detail kategori (AJAX)
    public function show_ajax($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => false,
                'message' => 'Data kategori tidak ditemukan'
            ], 404);
        }

        return view('kategori.show_ajax', compact('kategori'));
    }

    // Menampilkan form edit kategori (AJAX)
    public function edit_ajax($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => false,
                'message' => 'Data kategori tidak ditemukan'
            ], 404);
        }

        return view('kategori.edit_ajax', compact('kategori'));
    }

    // Menyimpan perubahan data kategori (AJAX)
    public function update_ajax(Request $request, $id)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|max:10|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
            'kategori_nama' => 'required|string|max:100',
        ]);

        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => false,
                'message' => 'Data kategori tidak ditemukan'
            ], 404);
        }

        $kategori->update([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data kategori berhasil diubah',
            'data' => $kategori
        ]);
    }

    // Menampilkan konfirmasi penghapusan (AJAX)
    public function confirm_ajax($id)
    {
        $kategori = KategoriModel::find($id);

        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    // Menghapus data kategori (AJAX)
    public function delete_ajax(Request $request,$id) {
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = KategoriModel::find($id);
            try{
                if ($kategori) {
                    $kategori->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                ]);
            }
        }
        return redirect('/');
    }
}