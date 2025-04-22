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
        .d-block { display: block; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-10 { font-size: 10pt; }
        .font-11 { font-size: 11pt; }
        .font-bold { font-weight: bold; }
        .border-all, .border-all th, .border-all td {
            border: 1px solid;
        }
        .border-bottom-header {
            border-bottom: 1px solid;
        }
        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }
    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('polinema-bw.png') }}" class="image">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101105, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN PENJUALAN PER TRANSAKSI</h3>

    <div class="font-11">
        <span class="d-block">Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</span>
        <span class="d-block">Total Transaksi: {{ count($penjualan) }}</span>
    </div>

    @foreach($penjualan as $p)
        @php
            $totalItem = $p->detail->sum('jumlah');
            $totalHarga = $p->detail->sum(fn($d) => $d->jumlah * $d->harga);
        @endphp

        <table style="margin-top: 20px; font-size: 11pt;">
            <tr><td><strong>Kode Penjualan:</strong> {{ $p->penjualan_kode }}</td></tr>
            <tr><td><strong>Tanggal:</strong> {{ date('d/m/Y', strtotime($p->penjualan_tanggal)) }}</td></tr>
            <tr><td><strong>Pembeli:</strong> {{ $p->pembeli }}</td></tr>
            <tr><td><strong>User:</strong> {{ $p->user->nama ?? '-' }}</td></tr>
        </table>

        <table class="border-all font-11" style="margin-top: 5px;">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($p->detail as $i => $d)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $d->barang_nama }}</td>
                        <td class="text-center">{{ $d->jumlah }}</td>
                        <td class="text-right">{{ number_format($d->harga, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($d->jumlah * $d->harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-right font-bold">Total</td>
                    <td class="text-right font-bold">{{ number_format($totalHarga, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <div style="margin-top: 40px;">
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
