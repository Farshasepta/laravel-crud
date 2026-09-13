@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')

@section('content')
<div class="container mt-5">
    <h1>Tambah Mahasiswa</h1>
    <hr>
    <form action="{{ route('mahasiswa.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="mb-3 col-6">
                <label class="form-label">Program Studi</label>
                <select name="prodi" class="form-control @error('prodi') is-invalid @enderror" required>
                    <option value="">==> Pilih Prodi <==</option>
                    @foreach(['Teknik Informatika', 'Teknik Mesin', 'Teknik Listrik'] as $prodi)
                        <option value="{{ $prodi }}" @selected(old('prodi') === $prodi)>{{ $prodi }}</option>
                    @endforeach
                </select>
                @error('prodi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-6">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jk" class="form-control @error('jk') is-invalid @enderror" required>
                    <option value="">==> Pilih Jenis Kelamin <==</option>
                    @foreach(['Laki-Laki', 'Perempuan'] as $jk)
                        <option value="{{ $jk }}" @selected(old('jk') === $jk)>{{ $jk }}</option>
                    @endforeach
                </select>
                @error('jk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" class="form-control @error('telepon') is-invalid @enderror" name="telepon" value="{{ old('telepon') }}" required>
            @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Foto</label>
            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" onchange="previewImg()">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <img src="" alt="" class="img-thumbnail img-preview mt-2" width="100px" style="display: none;">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImg() {
    const foto = document.querySelector('#foto');
    const imgPreview = document.querySelector('.img-preview');
    if (!foto.files || !foto.files[0]) return;
    const fileFoto = new FileReader();
    fileFoto.readAsDataURL(foto.files[0]);
    fileFoto.onload = function(e) {
        imgPreview.src = e.target.result;
        imgPreview.style.display = 'block';
    };
}
</script>
@endpush
