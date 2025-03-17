<?php

// app/Http/Controllers/SupplierController.php
namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar supplier yang terdaftar dalam sistem',
        ];

        $activeMenu = 'supplier';

        return view('supplier.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    // Mengambil data supplier dalam bentuk JSON untuk DataTables
    public function list(Request $request)
    {
        $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama');

        // Filter berdasarkan supplier_kode
        if ($request->supplier_kode) {
            $suppliers->where('supplier_kode', 'like', '%' . $request->supplier_kode . '%');
        }

        return DataTables::of($suppliers)
            ->addIndexColumn() // Menambahkan kolom index / no urut
            ->addColumn('aksi', function ($supplier) { // Menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // Memberitahu bahwa kolom aksi berisi HTML
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'supplier_kode' => 'required|string|min:3|unique:m_supplier,supplier_kode',
            'supplier_nama' => 'required|string|max:100',
        ]);

        SupplierModel::create([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
    }

    public function show($id)
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail supplier',
        ];

        $activeMenu = 'supplier';

        return view('supplier.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit($id)
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit supplier',
        ];

        $activeMenu = 'supplier';

        return view('supplier.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_kode' => 'required|string|min:3|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
            'supplier_nama' => 'required|string|max:100',
        ]);

        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $supplier->update([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil diubah');
    }

    public function destroy($id)
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            $supplier->delete();
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    // Menyimpan data supplier baru (AJAX)
    public function store_ajax(Request $request)
    {
        $request->validate([
            'supplier_kode' => 'required|string|min:3|max:10|unique:m_supplier,supplier_kode',
            'supplier_nama' => 'required|string|max:100',
        ]);

        $supplier = SupplierModel::create([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data supplier berhasil disimpan',
            'data' => $supplier
        ]);
    }

    public function edit_ajax($id)
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'Data supplier tidak ditemukan'
            ], 404);
        }

        return view('supplier.edit_ajax', compact('supplier'));
    }

    // Menyimpan perubahan data supplier (AJAX)
    public function update_ajax(Request $request, $id)
    {
        $request->validate([
            'supplier_kode' => 'required|string|min:3|max:10|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
            'supplier_nama' => 'required|string|max:100',
        ]);

        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'Data supplier tidak ditemukan'
            ], 404);
        }

        $supplier->update([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data supplier berhasil diubah',
            'data' => $supplier
        ]);
    }

    // Menampilkan konfirmasi penghapusan (AJAX)
    public function confirm_ajax($id)
    {
        $supplier = SupplierModel::find($id);

        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }

    // Menghapus data supplier (AJAX)
    public function delete_ajax(Request $request,$id) {
        if ($request->ajax() || $request->wantsJson()) {
            $user = SupplierModel::find($id);
            if ($user) {
                $user->delete();
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
        }
        return redirect('/');
    }
}