@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Level</h1>
    <form action="{{ route('levels.update', $level->level_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="level_kode">Kode:</label>
            <input type="text" name="level_kode" class="form-control" value="{{ $level->level_kode }}" required>
        </div>
        <div class="form-group">
            <label for="level_nama">Nama:</label>
            <input type="text" name="level_nama" class="form-control" value="{{ $level->level_nama }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection