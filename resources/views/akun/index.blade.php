@extends('layouts.app')

@section('title', 'Data Akun')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="nav-icon fas fa-user-cog"></i> Data Akun</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Data Akun</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tabel Data Akun</h3>
                <div class="card-tools">
                    @if($isAdmin)
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah"><i class="fas fa-plus"></i> Tambah</button>
                    @endif
                </div>
            </div>
            <div class="card-body table-responsive">
                <table id="akunTable" class="table table-bordered table-hover">
                    <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Email</th><th>Password</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($dataAkun as $akun)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $akun->nama }}</td>
                            <td>{{ $akun->username }}</td>
                            <td>{{ $akun->email }}</td>
                            <td>Password Ter-enkripsi</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalUbah{{ $akun->id_akun }}"><i class="fas fa-edit"></i> Ubah</button>
                                @if($isAdmin)
                                <form action="{{ route('akun.destroy', $akun->id_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun {{ $akun->nama }}?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@if($isAdmin)
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary"><h5 class="modal-title">Tambah Akun</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form action="{{ route('akun.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level" class="form-control" required>
                            <option value="1">Admin</option>
                            <option value="2">Operator Barang</option>
                            <option value="3">Operator Mahasiswa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@foreach($dataAkun as $akun)
<div class="modal fade" id="modalUbah{{ $akun->id_akun }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success"><h5 class="modal-title">Ubah Akun</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form action="{{ route('akun.update', $akun->id_akun) }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" value="{{ $akun->nama }}" required></div>
                    <div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" value="{{ $akun->username }}" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $akun->email }}" required></div>
                    <div class="form-group"><label>Password <small>(Kosongkan jika tidak diubah)</small></label><input type="password" name="password" class="form-control" minlength="6"></div>
                    @if($isAdmin)
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level" class="form-control" required>
                            <option value="1" @selected($akun->level == '1')>Admin</option>
                            <option value="2" @selected($akun->level == '2')>Operator Barang</option>
                            <option value="3" @selected($akun->level == '3')>Operator Mahasiswa</option>
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="level" value="{{ $akun->level }}">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
