@extends('layouts.app')

@section('title', 'Ubah Pegawai')

@section('content')
<div class="container mt-5">
    <h1>Ubah Pegawai</h1>
    <hr>
    <form action="{{ route('pegawai.update', $pegawai->id_pegawai) }}" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3"><label class="form-label">Nama</label><input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $pegawai->nama) }}" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Jabatan</label><input type="text" class="form-control @error('jabatan') is-invalid @enderror" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}" required>@error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $pegawai->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Telepon</label><input type="text" class="form-control @error('telepon') is-invalid @enderror" name="telepon" value="{{ old('telepon', $pegawai->telepon) }}" required>@error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat', $pegawai->alamat) }}</textarea>@error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label">Tanggal Masuk</label><input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', $pegawai->tanggal_masuk) }}">@error('tanggal_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
