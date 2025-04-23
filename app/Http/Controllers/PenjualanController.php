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
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

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
                $btn = '<button onclick="modalAction(\''.url('/penjualan/'.$row->penjualan_id.'/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
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

    public function show_ajax($id)
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])->findOrFail($id);
        $barang = BarangModel::all();
        $user = UserModel::all();
        $penjualanDetail = PenjualanDetailModel::where('penjualan_id', $id)->get();
        
        // Hitung total dari detail penjualan
        $total = $penjualan->details->sum(function ($detail) {
            return $detail->harga * $detail->jumlah;
        });

        return view('penjualan.show_ajax', [
            'penjualan' => $penjualan,
            'barang' => $barang,
            'user' => $user,
            'penjualanDetail' => $penjualanDetail,
            'total' => $total
        ]);
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

    public function update_ajax(Request $request, $id)
    {
        $penjualan = PenjualanModel::with('details')->find($id);
        if (!$penjualan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
    
        // Validasi header
        $validator = Validator::make($request->all(), [
            'penjualan_tanggal' => 'required|date',
            'pembeli' => 'required|max:50',
            'user_id' => 'required|exists:m_user,user_id',
            'items' => 'required|json',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()]);
        }
    
        $items = json_decode($request->items, true);
        $itemErrors = [];
        foreach ($items as $i => $item) {
            if (empty($item['barang_id'])) {
                $itemErrors["barang_id"] = ['Barang wajib dipilih.'];
            }
            if (empty($item['harga']) || $item['harga'] <= 0) {
                $itemErrors["harga"] = ['Harga tidak valid.'];
            }
            if (empty($item['jumlah']) || $item['jumlah'] <= 0) {
                $itemErrors["jumlah"] = ['Jumlah tidak valid.'];
            }
        }
    
        if (!empty($itemErrors)) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal', 'errors' => $itemErrors]);
        }
    
        // Simpan header
        $penjualan->update([
            'penjualan_tanggal' => $request->penjualan_tanggal,
            'pembeli' => $request->pembeli,
            'user_id' => $request->user_id,
        ]);
    
        // Hapus detail lama & insert baru
        PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)->delete();
        foreach ($items as $item) {
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $item['barang_id'],
                'harga' => $item['harga'],
                'jumlah' => $item['jumlah'],
            ]);
        }
    
        return response()->json(['status' => true, 'message' => 'Penjualan berhasil diperbarui']);
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
        try {
            $penjualan = PenjualanModel::findOrFail($id);
    
            // Hapus dulu semua detail yang terkait
            $penjualan->details()->delete();
    
            // Lalu hapus penjualan
            $penjualan->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data penjualan: ' . $e->getMessage()
            ]);
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
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            try {
                $file = $request->file('file_penjualan');
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = public_path('files/penjualan/');
                
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }
                
                $file->move($filePath, $fileName);

                $spreadsheet = IOFactory::load($filePath . $fileName);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, true, true, true);

                // Validasi header
                $header = array_shift($data);
                $expectedHeaders = [
                    'A' => 'Kode Penjualan',
                    'B' => 'Tanggal Penjualan',
                    'C' => 'Pembeli',
                    'D' => 'Barang ID',
                    'E' => 'Jumlah',
                    'F' => 'Harga',
                    'G' => 'ID User'
                ];

                foreach ($expectedHeaders as $col => $expected) {
                    if (!isset($header[$col]) || $header[$col] !== $expected) {
                        throw new \Exception("Format header tidak sesuai. Kolom $col harus berisi '$expected'");
                    }
                }

                $successCount = 0;
                $errors = [];

                foreach ($data as $rowNum => $row) {
                    try {
                        // Skip baris kosong
                        if (empty(array_filter($row))) continue;

                        // Validasi data
                        $validator = Validator::make([
                            'kode' => $row['A'],
                            'tanggal' => $row['B'],
                            'pembeli' => $row['C'],
                            'barang_id' => $row['D'],
                            'jumlah' => $row['E'],
                            'harga' => $row['F'],
                            'user_id' => $row['G']
                        ], [
                            'kode' => 'required|string|max:20',
                            'tanggal' => [
                                'required',
                                function ($value, $fail) {
                                    if (!\Carbon\Carbon::createFromFormat('d/m/Y', $value)->isValid()) {
                                        $fail('Format tanggal harus d/m/Y');
                                    }
                                }
                            ],
                            'pembeli' => 'required|string|max:50',
                            'barang_id' => 'required|integer|exists:m_barang,barang_id',
                            'jumlah' => 'required|integer|min:1',
                            'harga' => 'required|numeric|min:0',
                            'user_id' => 'required|integer|exists:m_user,user_id'
                        ]);

                        if ($validator->fails()) {
                            $errors[] = "Baris $rowNum: " . implode(', ', $validator->errors()->all());
                            continue;
                        }

                        // Format tanggal dari d/m/Y ke Y-m-d
                        $tanggal = \Carbon\Carbon::createFromFormat('d/m/Y', $row['B'])->format('Y-m-d');

                        // Cek atau buat penjualan
                        $penjualan = PenjualanModel::updateOrCreate(
                            ['penjualan_kode' => $row['A']],
                            [
                                'penjualan_tanggal' => $tanggal,
                                'pembeli' => $row['C'],
                                'user_id' => $row['G']
                            ]
                        );

                        // Buat detail penjualan
                        PenjualanDetailModel::create([
                            'penjualan_id' => $penjualan->penjualan_id,
                            'barang_id' => $row['D'],
                            'jumlah' => $row['E'],
                            'harga' => $row['F']
                        ]);

                        $successCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris $rowNum: " . $e->getMessage();
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => $successCount > 0,
                    'message' => "Import selesai. Berhasil: $successCount, Gagal: " . count($errors),
                    'errors' => $errors
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
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

    public function export_excel()
    {
        // Ambil semua penjualan beserta kasir dan detail→barang
        $penjualans = PenjualanModel::with(['user','details.barang'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1','Kode Penjualan');
        $sheet->setCellValue('B1','Tanggal');
        $sheet->setCellValue('C1','Kasir');
        $sheet->setCellValue('D1','Pembeli');
        $sheet->setCellValue('E1','Nama Barang');
        $sheet->setCellValue('F1','Harga');
        $sheet->setCellValue('G1','Jumlah');
        $sheet->setCellValue('H1','Subtotal');

        // Isi data
        $row = 2;
        foreach ($penjualans as $penjualan) {
            foreach ($penjualan->details as $detail) {
                $sheet->setCellValue('A'.$row, $penjualan->penjualan_kode);
                $sheet->setCellValue('B'.$row, $penjualan->penjualan_tanggal);
                $sheet->setCellValue('C'.$row, $penjualan->user->nama ?? '-');
                $sheet->setCellValue('D'.$row, $penjualan->pembeli ?? '-');
                $sheet->setCellValue('E'.$row, $detail->barang->barang_nama ?? '-');
                $sheet->setCellValue('F'.$row, $detail->harga);
                $sheet->setCellValue('G'.$row, $detail->jumlah);
                $sheet->setCellValue('H'.$row, $detail->harga * $detail->jumlah);
                $row++;
            }
        }

        // Auto-size kolom
        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        // Kirim output ke browser
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'data_penjualan_'.date('Ymd_His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf() {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])
        ->orderByDesc('penjualan_tanggal')
        ->get();

        $pdf = PDF::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('A4', 'potrait');
        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
