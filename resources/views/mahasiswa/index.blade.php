@extends('layouts.app')

@section('title', 'Daftar Mahasiswa')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-user-graduate"></i> Data Mahasiswa</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Data Mahasiswa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Grafik Mahasiswa Berdasarkan Prodi</h3></div>
                    <div class="card-body"><canvas id="chartProdi" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Grafik Mahasiswa Berdasarkan Jenis Kelamin</h3></div>
                    <div class="card-body"><canvas id="chartJK" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tabel Data Mahasiswa</h3>
                        <div class="card-tools">
                            <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle"></i> Tambah</a>
                            <a href="{{ route('mahasiswa.excel') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Download Excel</a>
                            <a href="{{ route('mahasiswa.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> Download PDF</a>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="mahasiswaTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th><th>Nama</th><th>Prodi</th><th>Jenis Kelamin</th><th>Telepon</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($dataMahasiswa as $mahasiswa)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $mahasiswa->nama }}</td>
                                    <td>{{ $mahasiswa->prodi }}</td>
                                    <td>{{ $mahasiswa->jk }}</td>
                                    <td>{{ $mahasiswa->telepon }}</td>
                                    <td class="text-center" width="15%">
                                        <a href="{{ route('mahasiswa.show', $mahasiswa->id_mahasiswa) }}" class="btn btn-secondary btn-sm">Detail</a>
                                        <a href="{{ route('mahasiswa.edit', $mahasiswa->id_mahasiswa) }}" class="btn btn-success btn-sm">Ubah</a>
                                        <form action="{{ route('mahasiswa.destroy', $mahasiswa->id_mahasiswa) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin Data Mahasiswa Akan Dihapus?');">
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
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
var prodiLabels = @json($prodiData->pluck('prodi'));
var prodiValues = @json($prodiData->pluck('jumlah')->map(fn($v) => (int) $v));
var bgColors = ['#17a2b8','#28a745','#ffc107','#dc3545','#6610f2','#fd7e14','#20c997','#e83e8c','#6f42c1','#007bff'];
new Chart(document.getElementById('chartProdi').getContext('2d'), {
    type: 'bar',
    data: { labels: prodiLabels, datasets: [{ label: 'Jumlah Mahasiswa', data: prodiValues, backgroundColor: bgColors.slice(0, prodiLabels.length) }] },
    options: { responsive: true, maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1, precision: 0 } }] }, legend: { display: false } }
});

var jkLabels = @json($jkData->pluck('jk'));
var jkValues = @json($jkData->pluck('jumlah')->map(fn($v) => (int) $v));
new Chart(document.getElementById('chartJK').getContext('2d'), {
    type: 'doughnut',
    data: { labels: jkLabels, datasets: [{ data: jkValues, backgroundColor: ['#17a2b8','#dc3545','#28a745'] }] },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
@endpush
