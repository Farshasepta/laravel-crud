@extends('layouts.app')

@section('title', 'Kirim Email')

@section('content')
<div class="container mt-5">
    <h1><i class="fas fa-envelope"></i> Kirim Email</h1>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('email.send') }}" method="post">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email Penerima</label>
            <input type="email" class="form-control @error('email_penerima') is-invalid @enderror" name="email_penerima" value="{{ old('email_penerima') }}" required>
            @error('email_penerima')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" required>
            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Pesan</label>
            <textarea name="pesan" class="form-control @error('pesan') is-invalid @enderror" rows="10" required>{{ old('pesan') }}</textarea>
            @error('pesan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Kirim</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
