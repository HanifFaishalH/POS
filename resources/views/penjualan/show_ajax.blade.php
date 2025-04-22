<div class="modal-header">
    <h5 class="modal-title">Detail Transaksi #{{ $penjualan->penjualan_kode }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Tanggal:</strong>
            <p>{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->translatedFormat('d F Y H:i') }}</p>
        </div>
        <div class="col-md-6">
            <strong>Kasir:</strong>
            <p>{{ $penjualan->user->nama ?? '-' }}</p>
        </div>
        <div class="col-md-6">
            <strong>Pembeli:</strong>
            <p>{{ $penjualan->pembeli }}</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->barang_nama ?? '-' }}</td>
                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total:</th>
                    <th>Rp {{ number_format($penjualan->details->sum(function($item) { 
                        return $item->harga * $item->jumlah; 
                    }), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>