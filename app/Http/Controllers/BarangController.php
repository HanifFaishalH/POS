<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;


class BarangController extends Controller
{
    public function index()
    {
        $activeMenu = 'barang';
        $breadcrumb = (object) [
            'title' => 'Data Barang',
            'list' => ['Home', 'Barang']
        ];

        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.index', [
            'activeMenu' => $activeMenu,
            'breadcrumb' => $breadcrumb,
            'kategori' => $kategori
        ]);
    }

    public function list(Request $request)
    {
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')->with('kategori');

        if ($request->input('filter_kategori')) {
            $barang->where('kategori_id', $request->input('filter_kategori'));
        }

        return DataTables::of($barang)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn = '<button onclick="modalAction(\''.url('/barang/'.$barang->barang_id.'/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/barang/'.$barang->barang_id.'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/barang/'.$barang->barang_id.'/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('barang.create_ajax')->with('kategori', $kategori);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_id' => ['required', 'integer', 'exists:m_kategori,kategori_id'],
                'barang_kode' => ['required', 'min:3', 'max:20', 'unique:m_barang,barang_kode'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli' => ['required', 'numeric'],
                'harga_jual' => ['required', 'numeric'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            BarangModel::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax($id)
    {
        $barang = BarangModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('barang.edit_ajax', ['barang' => $barang, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_id' => ['required', 'integer', 'exists:m_kategori,kategori_id'],
                'barang_kode' => ['required', 'min:3', 'max:20', 'unique:m_barang,barang_kode,' . $id . ',barang_id'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli' => ['required', 'numeric'],
                'harga_jual' => ['required', 'numeric'],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = BarangModel::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function confirm_ajax($id)
    {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function import()
    {
        return view('barang.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_barang' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'] // Tambah xls dan perbesar max size
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors()
                ], 422); // Tambah HTTP status code
            }

            try {
                $file = $request->file('file_barang');
                $fileName = time().'_'.$file->getClientOriginalName(); // Tambah timestamp untuk unique filename
                $filePath = public_path('files/barang/');
                
                // Buat direktori jika belum ada
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                // Pindahkan file ke folder tujuan
                $file->move($filePath, $fileName);

                // Baca file dari lokasi baru
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($filePath . $fileName);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insert = [];
                $rowErrors = [];
                
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // Skip header row
                        // Validasi kolom
                        if (empty($value['A']) || empty($value['B']) || empty($value['C']) || 
                            empty($value['D']) || empty($value['E'])) {
                            $rowErrors[] = "Baris $baris: Data tidak lengkap";
                            continue;
                        }

                        // Validasi numerik untuk harga
                        if (!is_numeric($value['D']) || !is_numeric($value['E'])) {
                            $rowErrors[] = "Baris $baris: Format harga tidak valid";
                            continue;
                        }

                        $insert[] = [
                            'kategori_id' => $value['A'],
                            'barang_kode' => $value['B'],
                            'barang_nama' => $value['C'],
                            'harga_beli' => (float)$value['D'],
                            'harga_jual' => (float)$value['E'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    BarangModel::insert($insert); // Ganti insertOrIgnore dengan insert biasa
                    
                    $response = [
                        'status' => true,
                        'message' => 'Data berhasil diimport',
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
                \Log::error('Import Error: ' . $e->getMessage()); // Log error
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
        $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
        ->orderBy('kategori_id')
        ->with('kategori')
        ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kategori');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Harga Beli');
        $sheet->setCellValue('F1', 'Harga Jual');

        $sheet ->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($barang as $key => $value) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $value->kategori->kategori_nama);
            $sheet->setCellValue('C' . $row, $value->barang_kode);
            $sheet->setCellValue('D' . $row, $value->barang_nama);
            $sheet->setCellValue('E' . $row, $value->harga_beli);
            $sheet->setCellValue('F' . $row, $value->harga_jual);
            $no++;
            $row++;
        }

        foreach(range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Barang');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'data_barang_' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer->save('php://output');
    }

    public function export_pdf() {
        $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
        ->orderBy('kategori_id')
        ->orderBy('barang_kode')
        ->with('kategori')
        ->get();

        $pdf = PDF::loadView('barang.export_pdf', ['barang' => $barang]);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->stream('Data Barang' . date('Y-m-d H:i:s') . '.pdf');
    }
}