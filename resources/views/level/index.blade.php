@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">{{ $page->title }}</h3>
      <div class="card-tools">
        <a class="btn btn-sm btn-primary mt-1" href="{{ url('level/create') }}">Tambah</a>
      </div>
    </div>
    <div class="card-body">
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
        <thead>
          <tr>
            <th>ID</th>
            <th>Kode Level</th>
            <th>Nama Level</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($level as $item)
            <tr>
              <td>{{ $item->level_id }}</td>
              <td>{{ $item->level_kode }}</td>
              <td>{{ $item->level_nama }}</td>
              <td>
                <a href="{{ url('level/' . $item->level_id) }}" class="btn btn-info btn-sm">Detail</a>
                <a href="{{ url('level/' . $item->level_id . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
                <form class="d-inline-block" method="POST" action="{{ url('level/' . $item->level_id) }}">
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