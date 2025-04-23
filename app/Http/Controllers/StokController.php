<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokModel;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;


class StokController extends Controller
{
    public function index()
    {
        $activeMenu = 'stok';
        $breadcrumb = (object) [
            'title' => 'Stok Barang',
            'list' => ['Home', 'Stok']
        ];
    
        $page = (object) [
            'title' => 'Stok barang yang tersedia dalam sistem',
        ];
    

        $stok = StokModel::select('stok_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')->get();

        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')->get();

        $supplier = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama');
        
        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'stok' => $stok,
            'kategori' => $kategori,
            'barang' => $barang,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request) {
        $stok = StokModel::with(['barang.kategori', 'user', 'supplier'])
        ->select('stok_id', 'barang_id', 'user_id', 'supplier_id', 'stok_tanggal', 'stok_jumlah')
        ->get();


         // Filter berdasarkan kategori
        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $stok->whereHas('barang', function($query) use ($request) {
                $query->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter berdasarkan barang
        if ($request->has('barang_id') && $request->barang_id != '') {
            $stok->where('barang_id', $request->barang_id);
        }

        return DataTables::of($stok)
        ->addIndexColumn()
        ->addColumn('barang_id', function ($row) {
            return $row->barang->barang_kode ?? '-';
        })
        ->addColumn('barang_nama', function ($row) {
            return $row->barang->barang_nama ?? '-';
        })
        ->addColumn('kategori_nama', function ($row) {
            return $row->barang->kategori->kategori_nama ?? '-';
        })
        ->addColumn('user_nama', function ($row) {
            return $row->user->nama ?? '-';
        })
        ->addColumn('supplier_nama', function ($row) {
            return $row->supplier->supplier_nama ?? '-';
        })
        ->addColumn('aksi', function ($row) {
            $btn = '<button onclick="modalAction(\''.url('/stok/'.$row->stok_id.'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
            $btn .= '<button onclick="modalAction(\''.url('/stok/'.$row->stok_id.'/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
            return $btn;
        })
        ->rawColumns(['aksi'])
        ->make(true);

    }

    public function create_ajax() 
    {
        $barang = BarangModel::all();
        $user = UserModel::all();
        $supplier = SupplierModel::all();
        
        return view('stok.create_ajax', [
            'barang' => $barang,
            'user' => $user,
            'supplier' => $supplier
        ]);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi data utama
            $validator = Validator::make($request->all(), [
                'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
                'penjualan_tanggal' => 'required|date',
                'pembeli' => 'required|string|max:50',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'items' => 'required|json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ], 422);
            }

            // Decode items
            $items = json_decode($request->items, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => false,
                    'message' => 'Format items tidak valid'
                ], 422);
            }

            // Validasi items
            $itemErrors = [];
            foreach ($items as $index => $item) {
                $itemValidator = Validator::make($item, [
                    'barang_id' => 'required|integer|exists:m_barang,barang_id',
                    'harga' => 'required|integer|min:1',
                    'jumlah' => 'required|integer|min:1'
                ]);

                if ($itemValidator->fails()) {
                    $itemErrors["items.$index"] = $itemValidator->errors()->all();
                }
            }

            if (!empty($itemErrors)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi item gagal',
                    'msgField' => ['items' => $itemErrors]
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Simpan penjualan utama
                $penjualan = PenjualanModel::create([
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                    'pembeli' => $request->pembeli,
                    'user_id' => $request->user_id
                ]);

                // Simpan detail penjualan
                foreach ($items as $item) {
                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $item['barang_id'],
                        'harga' => $item['harga'],
                        'jumlah' => $item['jumlah'],
                        'subtotal' => $item['harga'] * $item['jumlah']
                    ]);

                    // Update stok barang
                    BarangModel::where('barang_id', $item['barang_id'])
                        ->decrement('stok', $item['jumlah']);
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil disimpan',
                    'data' => $penjualan
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
                ], 500);
            }
        }

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil disimpan');
    }

    public function show_ajax($id) {
        $stok = StokModel::find($id);

        if (!$stok) {
            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
            ], 404);
        }

        return view('stok.show', ['stok' => $stok]);
    }

    public function edit_ajax($id) {
        $stok = StokModel::find($id);
        $barang = BarangModel::all();
        $user = UserModel::all();
    
        if (!$stok) {
            return response()->json([
                'success' => false,
                'message' => 'Data stok tidak ditemukan'
            ], 404);
        }
    
        return view('stok.edit_ajax', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function update_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => ['required', 'integer', 'exists:m_barang,barang_id'],
                'user_id' => ['required', 'integer', 'exists:m_user,user_id'],
                'stok_tanggal' => ['required', 'date'],
                'stok_jumlah' => ['required', 'integer'],
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan'
                ], 404);
            }
        }
    
        return redirect('/stok')->with('success', 'Data stok berhasil diupdate');
    }

    public function confirm_ajax($id) {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);
            try {
                if ($stok) {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data stok tidak ditemukan'
                    ], 404);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menghapus data stok: ' . $e->getMessage()
                ], 500);
            }
        }
    
        return redirect('/stok')->with('success', 'Data stok berhasil dihapus');
    }

    public function destroy_ajax($id)
    {
        $stok = StokModel::find($id);

        if (!$stok) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        try{
            $stok->delete();
            return redirect('/stok')->with('success', 'Data stok berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/stok')->with('error', 'Data stok gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $file = $request->file('file_stok');
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = public_path('files/stok/');
                
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                $file->move($filePath, $fileName);

                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($filePath . $fileName);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insert = [];
                $rowErrors = [];
                
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // Skip header row
                        if (empty($value['A']) || empty($value['B']) || empty($value['C']) || empty($value['D']) || empty($value['E'])) {
                            $rowErrors[] = "Baris $baris: Data tidak lengkap";
                            continue;
                        }
                
                        $barang = BarangModel::where('barang_kode', $value['A'])->where('barang_nama', $value['B'])->first();
                        $supplier = SupplierModel::find($value['E']);
                        $user = auth()->user(); // Asumsi import dilakukan oleh user yang login
                
                        if (!$barang || !$supplier || !$user) {
                            $rowErrors[] = "Baris $baris: Barang, Supplier, atau User tidak ditemukan";
                            continue;
                        }
                
                        if (!is_numeric($value['C']) || $value['C'] < 1) {
                            $rowErrors[] = "Baris $baris: Jumlah stok tidak valid";
                            continue;
                        }
                
                        $insert[] = [
                            'barang_id' => $barang->barang_id,
                            'user_id' => $user->user_id,
                            'stok_jumlah' => (int)$value['C'],
                            'supplier_id' => $supplier->supplier_id,
                            'stok_tanggal' => now()->format('Y-m-d'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }                

                if (count($insert) > 0) {
                    StokModel::insert($insert);
                    
                    $response = [
                        'status' => true,
                        'message' => 'Data stok berhasil diimport',
                        'imported_count' => count($insert),
                    ];
                    
                    if (!empty($rowErrors)) {
                        $response['warning'] = 'Beberapa data tidak diimport';
                        $response['errors'] = $rowErrors;
                    }
                    
                    return response()->json($response);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tidak ada data valid yang bisa diimport',
                        'errors' => $rowErrors
                    ], 400);
                }
            } catch (\Exception $e) {
                \Log::error('Import Stok Error: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memproses file',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Permintaan tidak valid'
        ], 400);
    }

    public function export_excel() {
        $stok = StokModel::select('barang_id', 'user_id', 'supplier_id', 'stok_jumlah', 'stok_tanggal')
            ->orderBy('stok_tanggal', 'desc')
            ->with(['barang.kategori', 'supplier'])
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

             // Header tanpa kolom User
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Kategori');
        $sheet->setCellValue('E1', 'Jumlah Stok');
        $sheet->setCellValue('F1', 'Supplier');
        $sheet->setCellValue('G1', 'Tanggal Stok');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Data
        $no = 1;
        $row = 2;
        foreach ($stok as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->barang->barang_kode ?? '-');
            $sheet->setCellValue('C' . $row, $item->barang->barang_nama ?? '-');
            $sheet->setCellValue('D' . $row, $item->barang->kategori->kategori_nama ?? '-');
            $sheet->setCellValue('E' . $row, $item->stok_jumlah);
            $sheet->setCellValue('F' . $row, $item->supplier->supplier_nama ?? '-');
            $sheet->setCellValue('G' . $row, $item->stok_tanggal);
            $no++;
            $row++;
        }

        // Auto size columns
        foreach(range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Stok');

        // Download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'data_stok_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
            $writer->save('php://output');
    }

    public function export_pdf() {
        $stok = StokModel::with(['barang.kategori', 'user'])
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        $pdf = PDF::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        
        return $pdf->stream('Data_Stok_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
