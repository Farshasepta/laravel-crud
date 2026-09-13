@extends('layouts.app')

@section('title', 'Ubah Barang')

@section('content')
<div class="content-wrapper">
    <div class="container mt-5">
        <h1>Ubah Barang</h1>
        <hr>
        <form action="{{ route('barang.update', $barang->id_barang) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $barang->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Barang</label>
                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ old('jumlah', $barang->jumlah) }}" required>
                @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" class="form-control @error('harga') is-invalid @enderror" name="harga" value="{{ old('harga', $barang->harga) }}" required>
                @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Ubah</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
