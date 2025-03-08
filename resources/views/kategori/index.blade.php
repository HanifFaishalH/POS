@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">{{ $page->title }}</h3>
      <div class="card-tools">
        <a class="btn btn-sm btn-primary mt-1" href="{{ url('kategori/create') }}">Tambah</a>
      </div>
    </div>
    <div class="card-body">
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori">
        <thead>
          <tr>
            <th>ID</th>
            <th>Kode Kategori</th>
            <th>Nama Kategori</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($kategori as $item)
            <tr>
              <td>{{ $item->kategori_id }}</td>
              <td>{{ $item->kategori_kode }}</td>
              <td>{{ $item->kategori_nama }}</td>
              <td>
                <a href="{{ url('kategori/' . $item->kategori_id) }}" class="btn btn-info btn-sm">Detail</a>
                <a href="{{ url('kategori/' . $item->kategori_id . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
                <form class="d-inline-block" method="POST" action="{{ url('kategori/' . $item->kategori_id) }}">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin menghapus data ini?');">Hapus</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush