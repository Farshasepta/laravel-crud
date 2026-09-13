@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="container mt-5">
    <h1>Data {{ $mahasiswa->nama }}</h1>
    <hr>
    <table class="table table-bordered mt-3">
        <tr><td>Nama</td><td>: {{ $mahasiswa->nama }}</td></tr>
        <tr><td>Program Studi</td><td>: {{ $mahasiswa->prodi }}</td></tr>
        <tr><td>Jenis Kelamin</td><td>: {{ $mahasiswa->jk }}</td></tr>
        <tr><td>Telepon</td><td>: {{ $mahasiswa->telepon }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $mahasiswa->alamat }}</td></tr>
        <tr><td>Email</td><td>: {{ $mahasiswa->email }}</td></tr>
        <tr>
            <td>Foto</td>
            <td>
                <a href="{{ asset('assets/img/'.$mahasiswa->foto) }}">
                    <img src="{{ asset('assets/img/'.$mahasiswa->foto) }}" alt="foto" width="50%">
                </a>
            </td>
        </tr>
    </table>
    <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
