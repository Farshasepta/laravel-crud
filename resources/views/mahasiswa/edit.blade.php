@extends('layouts.app')

@section('title', 'Ubah Mahasiswa')

@section('content')
<div class="container mt-5">
    <h1>Ubah Mahasiswa</h1>
    <hr>
    <form action="{{ route('mahasiswa.update', $mahasiswa->id_mahasiswa) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="mb-3 col-6">
                <label class="form-label">Program Studi</label>
                <select name="prodi" class="form-control" required>
                    @foreach(['Teknik Informatika', 'Teknik Mesin', 'Teknik Listrik'] as $prodi)
                        <option value="{{ $prodi }}" @selected(old('prodi', $mahasiswa->prodi) === $prodi)>{{ $prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-6">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jk" class="form-control" required>
                    @foreach(['Laki-Laki', 'Perempuan'] as $jk)
                        <option value="{{ $jk }}" @selected(old('jk', $mahasiswa->jk) === $jk)>{{ $jk }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" class="form-control" name="telepon" value="{{ old('telepon', $mahasiswa->telepon) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $mahasiswa->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto</label>
            <input type="file" class="form-control @error('foto') is-invalid @enderror" name="foto">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <p><small>Gambar Sebelumnya</small></p>
            <img src="{{ asset('assets/img/'.$mahasiswa->foto) }}" alt="foto" width="100px">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ubah</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
