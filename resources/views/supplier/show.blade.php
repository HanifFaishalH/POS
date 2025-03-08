@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Level Details</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">ID: {{ $level->level_id }}</h5>
            <p class="card-text">Kode: {{ $level->level_kode }}</p>
            <p class="card-text">Nama: {{ $level->level_nama }}</p>
        </div>
    </div>
    <a href="{{ route('levels.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection