@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Dashboard</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner"><h3>{{ $jumlahBarang }}</h3><p>Jumlah Barang</p></div>
                    <div class="icon"><i class="ion ion-bag"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner"><h3>{{ $jumlahMahasiswa }}</h3><p>Data Mahasiswa</p></div>
                    <div class="icon"><i class="ion ion-person"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner"><h3>{{ $jumlahPegawai }}</h3><p>Data Pegawai</p></div>
                    <div class="icon"><i class="ion ion-person-add"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner"><h3>{{ $jumlahAkun }}</h3><p>Data Akun</p></div>
                    <div class="icon"><i class="ion ion-pie-graph"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Grafik Statistik Data</h3></div>
                    <div class="card-body"><canvas id="chartDashboard" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Barang</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filter Data</button>
                            @if(in_array((string) Auth::user()->level, ['1', '2'], true))
                            <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th><th>Nama</th><th>Jumlah</th><th>Harga</th><th>Barcode</th><th>Tanggal</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = ($dataBarang->currentPage() - 1) * $dataBarang->perPage() + 1; @endphp
                                @foreach($dataBarang as $barang)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $barang->nama }}</td>
                                    <td>{{ $barang->jumlah }}</td>
                                    <td>Rp. {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <img alt="barcode" src="{{ route('barcode', ['codetype' => 'Code128', 'size' => 15, 'text' => $barang->barcode, 'print' => 'true']) }}" />
                                    </td>
                                    <td>{{ $barang->tanggal ? date('d/m/Y | H:i:s', strtotime($barang->tanggal)) : '-' }}</td>
                                    <td class="text-center" width="15%">
                                        @if(in_array((string) Auth::user()->level, ['1', '2'], true))
                                        <a href="{{ route('barang.edit', $barang->id_barang) }}" class="btn btn-success btn-sm">Edit</a>
                                        <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin Data Barang Akan Dihapus?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-2 justify-content-end d-flex">
                            {{ $dataBarang->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalFilter" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-filter"></i> Filter Data</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('dashboard') }}" method="get">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Awal</label>
                        <input type="date" name="tgl_awal" class="form-control" value="{{ $tglAwal }}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="tgl_akhir" class="form-control" value="{{ $tglAkhir }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" name="filter" value="1" class="btn btn-success"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var ctx = document.getElementById('chartDashboard').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Barang', 'Mahasiswa', 'Pegawai', 'Akun'],
        datasets: [{
            label: 'Jumlah Data',
            data: [{{ $jumlahBarang }}, {{ $jumlahMahasiswa }}, {{ $jumlahPegawai }}, {{ $jumlahAkun }}],
            backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545'],
            borderColor: ['#138496', '#1e7e34', '#d39e00', '#bd2130'],
            borderWidth: 1
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1, precision: 0 } }] }, legend: { display: false } }
});
</script>
@endpush
