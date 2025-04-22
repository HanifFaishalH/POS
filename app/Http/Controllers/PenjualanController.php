<?php

// app/Http/Controllers/PenjualanController.php
namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar transaksi penjualan'
        ];

        $activeMenu = 'penjualan';
        $barang = BarangModel::all();
        $user = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function list()
    {
        $penjualan = PenjualanModel::select(
            'penjualan_id',
            'user_id',
            'pembeli',
            'penjualan_kode',
            'penjualan_tanggal',
            'created_at'
        );

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->editColumn('penjualan_tanggal', function ($row) {
                return \Carbon\Carbon::parse($row->penjualan_tanggal)->format('Y-m-d H:i:s');
            })
            ->addColumn('kasir', function ($row) {
                return $row->user->nama ?? '-'; // Asumsikan ada relasi ke model User
            })
            ->addColumn('petugas', function ($row) {
                return 'Petugas Default'; // Ganti sesuai logika kamu
            })
            ->addColumn('total', function ($row) {
                return 'Rp 0'; // Ganti sesuai logika total jika tersedia
            })
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\''.url('/penjualan/'.$row->penjualan_id.'/detail_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/'.$row->penjualan_id.'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/'.$row->penjualan_id.'/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create_ajax() { 

        $barang = BarangModel::all(); // Sesuaikan dengan kebutuhan
        $user = UserModel::all();    // Sesuaikan dengan kebutuhan

        return view('penjualan.create_ajax', [
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function store_ajax(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'penjualan_kode' => 'required|string|max:20',
            'penjualan_tanggal' => 'required|date',
            'pembeli' => 'required|string|max:50',
            'user_id' => 'required|exists:m_user,user_id',
            'items' => 'required|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Decode items
        $items = json_decode($request->items, true);
        
        // Validasi items
        $itemValidator = Validator::make(['items' => $items], [
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:m_barang,barang_id',
            'items.*.harga' => 'required|integer|min:1',
            'items.*.jumlah' => 'required|integer|min:1'
        ]);

        if ($itemValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi item gagal',
                'errors' => $itemValidator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Simpan data penjualan utama
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
                    'jumlah' => $item['jumlah']
                ]);

                // Optional: Update stok barang
                BarangModel::where('barang_id', $item['barang_id'])
                    ->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil disimpan',
                'data' => $penjualan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan penjualan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show_ajax($id) {
        $penjualan = PenjualanModel::with([
            'user',
            'detail' => function ($query) {
                $query->with('barang');
            }
        ])->findOrFail($id);

        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }

    public function edit_ajax($id) {
        $penjualan = PenjualanModel::find($id);
        $barang = BarangModel::all();
        $user = UserModel::all();

        if (!$penjualan) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        return view('penjualan.edit_ajax', [
            'penjualan' => $penjualan,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function update_ajax(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m_user,user_id',
            'pembeli' => 'required|string|max:50',
            'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode,'.$id.',penjualan_id',
            'barang_id' => 'required|array',
            'barang_id.*' => 'integer|exists:m_barang,barang_id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'harga' => 'required|array',
            'harga.*' => 'numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Update penjualan header
            $penjualan = PenjualanModel::find($id);
            $penjualan->update([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode
            ]);

            // Delete existing details
            PenjualanDetailModel::where('penjualan_id', $id)->delete();

            // Create new details
            foreach ($request->barang_id as $key => $barang_id) {
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang_id,
                    'harga' => $request->harga[$key],
                    'jumlah' => $request->jumlah[$key]
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil diupdate',
                'data' => $penjualan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 500);
        }

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil diupdate');
    }

    public function confirm_ajax($id) {
        $penjualan = PenjualanModel::find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id) {
        $penjualan = PenjualanModel::find($id);

        if($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);
            try {
                if ($penjualan) {
                    $penjualan->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan tidak ditemukan'
                    ], 404);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menghapus data penjualan: ' . $e->getMessage()
                ], 500);
            }
        }

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
    }

    public function destroy_ajax($id) {
        $penjualan = PenjualanModel::find($id);

        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        try{
            $penjualan->delete();
            return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/penjualan')->with('error', 'Data penjualan gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_penjualan' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048']
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
                $file = $request->file('file_penjualan');
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = public_path('files/penjualan/');
                
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
                        // Validasi kolom (sesuaikan dengan struktur data penjualan)
                        if (empty($value['A']) || empty($value['B']) || empty($value['C']) || 
                            empty($value['D'])) {
                            $rowErrors[] = "Baris $baris: Data tidak lengkap";
                            continue;
                        }

                        // Validasi numerik untuk jumlah dan harga
                        if (!is_numeric($value['C']) || !is_numeric($value['D'])) {
                            $rowErrors[] = "Baris $baris: Format angka tidak valid";
                            continue;
                        }

                        $insert[] = [
                            'barang_id' => $value['A'],
                            'penjualan_tanggal' => $value['B'],
                            'jumlah' => (int)$value['C'],
                            'harga_total' => (float)$value['D'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    PenjualanModel::insert($insert);
                    
                    $response = [
                        'status' => true,
                        'message' => 'Data penjualan berhasil diimport',
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
                \Log::error('Import Error: ' . $e->getMessage());
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
        $penjualan = PenjualanDetailModel::with('penjualan', 'barang')
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Penjualan');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Harga');
        $sheet->setCellValue('F1', 'Subtotal');

        $no = 1;
        $row = 2;
        foreach ($penjualan as $detail) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $detail->penjualan->penjualan_tanggal);
            $sheet->setCellValue('C' . $row, $detail->barang->barang_nama);
            $sheet->setCellValue('D' . $row, $detail->jumlah);
            $sheet->setCellValue('E' . $row, $detail->harga);
            $sheet->setCellValue('F' . $row, $detail->harga * $detail->jumlah);
            $row++;
        }


        foreach(range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'data_penjualan_' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function export_pdf() {
        $penjualan = PenjualanDetailModel::with(['penjualan', 'barang'])
        ->orderByDesc('created_at')
        ->get();

        $pdf = PDF::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('A4', 'potrait');
        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
