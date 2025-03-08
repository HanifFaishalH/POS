@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('user/create') }}">Tambah</a>
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
                            <select class="form-control" id="level_id" name="level_id">
                                <option value="">- Pilih Level -</option>
                                @foreach($level as $item)
                                    <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Level Pengguna</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Level ID</th>
                        <th>Kode Level</th>
                        <th>Nama Level</th>
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
        $(document).ready(function () {
            var dataUser = $('#table_user').DataTable({
                serverSide: true, // Aktifkan server-side processing
                ajax: {
                    url: "{{ url('user/list') }}",
                    type: "POST",
                    dataType: "json",
                    data: function (d) {
                        d.level_id = $('#level_id').val(); // Kirim nilai filter ke server
                        d._token = "{{ csrf_token() }}"; // Tambahkan CSRF token
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex", // Kolom nomor urut
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "username",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "level_id",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "level.level_kode", // Sesuaikan dengan relasi di model
                        className: "",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "level.level_nama", // Sesuaikan dengan relasi di model
                        className: "",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "aksi",
                        className: "",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Event ketika filter diubah
            $('#level_id').on('change', function () {
                dataUser.ajax.reload(); // Reload DataTables
            });
        });
    </script>
@endpush