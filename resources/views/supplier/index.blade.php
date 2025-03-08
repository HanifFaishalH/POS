@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">{{ $page->title }}</h3>
      <div class="card-tools">
        <a href="{{ url('supplier/create') }}" class="btn btn-primary btn-sm">Tambah Supplier</a>
      </div>
    </div>
    <div class="card-body">
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      <table class="table table-bordered table-striped table-hover table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Kode Supplier</th>
            <th>Nama Supplier</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($suppliers as $supplier)
            <tr>
              <td>{{ $supplier->supplier_id }}</td>
              <td>{{ $supplier->supplier_kode }}</td>
              <td>{{ $supplier->supplier_nama }}</td>
              <td>
                <a href="{{ url('supplier/' . $supplier->supplier_id) }}" class="btn btn-info btn-sm">Detail</a>
                <a href="{{ url('supplier/' . $supplier->supplier_id . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
                <form class="d-inline-block" method="POST" action="{{ url('supplier/' . $supplier->supplier_id) }}">
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