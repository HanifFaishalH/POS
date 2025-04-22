@extends('layouts.template')

@section('content')
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
        <!-- Modal content will be loaded here -->
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('stok/create_ajax') }}')" class="btn btn-sm btn-primary mt-1">Tambah Ajax</button>
                <button onclick="modalAction('{{ url('stok/import') }}')" class="btn btn-sm btn-info mt-1"><i class="fas fa-file-import"></i> Import Excel
                </button>
                <a href="{{ url('stok/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel"></i> Export Excel</a>
                <a href="{{ url('stok/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file-pdf"></i> Export PDF</a>
                
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="kategori_id" name="kategori_id">
                                <option value="">- Pilih Kategori -</option>
                                @foreach($kategori as $items)
                                    <option value="{{ $items->kategori_id }}">{{ $items->kategori_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Kategori Barang</small>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
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

var dataStok;
$(document).ready(function() {
    dataStok = $('#table_stok').DataTable({
        serverSide: true,
        ajax: {
            url: "{{ url('stok/list') }}",
            dataType: "json",
            type: "POST",
            data: function (d) {
                d._token = '{{ csrf_token() }}';
            }
        },
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false },
            { data: "barang_id", name: "barang_id" },
            { data: "barang_nama", name: "barang_nama" },
            { data: "stok_jumlah", name: "stok_jumlah" },
            { data: "kategori_nama", name: "kategori_nama" },
            { data: "supplier_nama", name: "supplier_nama" },
            { data: "aksi", name: "aksi", orderable: false, searchable: false }
        ]
    });

    // Reload table when filter changes
    $('#kategori_id').on('change', function() {
        dataStok.ajax.reload();
    });
});
</script>
@endpush
