@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-users"></i> Data Pegawai</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Data Pegawai</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data</h3>
                <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button></div>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('pegawai.index') }}">
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" value="{{ $nama }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" class="form-control" value="{{ $jabatan }}"></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Tanggal Awal</label><input type="date" name="tgl_awal" class="form-control" value="{{ $tglAwal }}"></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Tanggal Akhir</label><input type="date" name="tgl_akhir" class="form-control" value="{{ $tglAkhir }}"></div></div>
                        <div class="col-md-2" style="display: flex; align-items: flex-end;"><div class="form-group w-100"><label>&nbsp;</label><button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Tampilkan</button></div></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Grafik Pegawai Berdasarkan Jabatan</h3></div>
                    <div class="card-body"><canvas id="chartJabatan" style="min-height: 300px; height: 300px; max-height: 400px; max-width: 100%;"></canvas></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tabel Data Pegawai</h3>
                <div class="card-tools"><a href="{{ route('pegawai.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</a></div>
            </div>
            <div class="card-body table-responsive">
                <table id="pegawaiTable" class="table table-bordered table-hover">
                    <thead>
                        <tr><th>No</th><th>Nama</th><th>Jabatan</th><th>Email</th><th>Telepon</th><th>Alamat</th><th>Tanggal Masuk</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($dataPegawai as $pegawai)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $pegawai->nama }}</td>
                            <td>{{ $pegawai->jabatan }}</td>
                            <td>{{ $pegawai->email }}</td>
                            <td>{{ $pegawai->telepon }}</td>
                            <td>{{ $pegawai->alamat }}</td>
                            <td>{{ $pegawai->tanggal_masuk ? date('d/m/Y', strtotime($pegawai->tanggal_masuk)) : '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('pegawai.edit', $pegawai->id_pegawai) }}" class="btn btn-success btn-sm">Edit</a>
                                <form action="{{ route('pegawai.destroy', $pegawai->id_pegawai) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data {{ $pegawai->nama }}?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
var jabatanLabels = @json($jabatanData->pluck('jabatan'));
var jabatanValues = @json($jabatanData->pluck('jumlah')->map(fn($v) => (int) $v));
var bgJabatan = ['#17a2b8','#28a745','#ffc107','#dc3545','#6610f2','#fd7e14','#20c997','#e83e8c','#6f42c1'];
new Chart(document.getElementById('chartJabatan').getContext('2d'), {
    type: 'horizontalBar',
    data: { labels: jabatanLabels, datasets: [{ label: 'Jumlah Pegawai', data: jabatanValues, backgroundColor: bgJabatan.slice(0, jabatanLabels.length) }] },
    options: { responsive: true, maintainAspectRatio: false, scales: { xAxes: [{ ticks: { beginAtZero: true, stepSize: 1, precision: 0 } }] }, legend: { display: false } }
});
</script>
@endpush
