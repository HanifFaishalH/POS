@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create New Level</h1>
    <form action="{{ route('levels.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="level_kode">Kode:</label>
            <input type="text" name="level_kode" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="level_nama">Nama:</label>
            <input type="text" name="level_nama" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection