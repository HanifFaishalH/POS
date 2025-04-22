<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 4px 3px;
        }
        th {
            text-align: left;
        }
        .d-block {
            display: block;
        }
        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .p-1 {
            padding: 5px 1px;
        }
        .font-10 {
            font-size: 10pt;
        }
        .font-11 {
            font-size: 11pt;
        }
        .font-12 {
            font-size: 12pt;
        }
        .font-13 {
            font-size: 13pt;
        }
        .font-bold {
            font-weight: bold;
        }
        .border-bottom-header {
            border-bottom: 1px solid;
        }
        .border-all, .border-all th, .border-all td {
            border: 1px solid;
        }
    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ asset('polinema-bw.png') }}" class="image">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">
                    Telepon (0341) 404424 Pes. 101105, 0341-404420, Fax. (0341) 404420
                </span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA STOK BARANG</h3>

    <div class="font-11 p-1">
        <span class="d-block">Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</span>
        <span class="d-block">Total Data: {{ count($stok) }}</span>
    </div>

    <table class="border-all font-11">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="text-center">Jumlah Stok</th>
                <th>User</th>
                <th class="text-center">Tanggal Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stok as $s)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $s->barang->barang_kode ?? '-' }}</td>
                    <td>{{ $s->barang->barang_nama ?? '-' }}</td>
                    <td>{{ $s->barang->kategori->kategori_nama ?? '-' }}</td>
                    <td class="text-center">{{ number_format($s->stok_jumlah, 0, ',', '.') }}</td>
                    <td>{{ $s->user->nama ?? '-' }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($s->stok_tanggal)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%;" class="text-center">
                    <div style="margin-top: 50px;">
                        <span class="d-block">Malang, {{ date('d F Y') }}</span>
                        <span class="d-block">Mengetahui,</span>
                        <div style="margin-top: 60px;">
                            <span class="d-block font-bold">(_______________________)</span>
                            <span class="d-block">NIP. ............................</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>