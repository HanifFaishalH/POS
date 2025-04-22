@extends('layouts.template')

@section('content')
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
        <!-- Modal content will be loaded here -->
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-sm btn-primary mt-1">Tambah Ajax</button>
                <button onclick="modalAction('{{ url('penjualan/import') }}')" class="btn btn-sm btn-info mt-1"><i class="fas fa-file-import"></i> Import Excel</button>
                <a href="{{ url('penjualan/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel"></i> Export Excel</a>
                <a href="{{ url('penjualan/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Customer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
<script>
function modalAction(url = '') {
    $('#myModal').load(url, function(response, status, xhr) {
        if (status == "error") {
            $('#myModal').html('<div class="alert alert-danger">Gagal memuat konten. Silakan coba lagi.</div>');
        }
        $('#myModal').modal('show');
    });
}

var dataPenjualan;
$(document).ready(function() {
    dataPenjualan = $('#table_penjualan').DataTable({
        serverSide: true,
        ajax: {
            url: "{{ url('penjualan/list') }}",
            type: "POST",
            data: function (d) {
                d._token = '{{ csrf_token() }}';
            }
        },
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false },
            { data: "penjualan_kode", name: "penjualan_kode" },
            { data: "penjualan_tanggal", name: "penjualan_tanggal" },
            { data: "pembeli", name: "pembeli" },
            { data: "aksi", name: "aksi", orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
