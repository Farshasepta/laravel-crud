<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Barang;
use App\Models\Mahasiswa;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $jumlahBarang = Barang::count();
        $jumlahMahasiswa = Mahasiswa::count();
        $jumlahPegawai = Pegawai::count();
        $jumlahAkun = Akun::count();

        $tglAwal = '';
        $tglAkhir = '';

        $barang = Barang::query()->orderByDesc('id_barang');

        if ($request->has('filter') && $request->input('tgl_awal') && $request->input('tgl_akhir')) {
            $tglAwal = $request->input('tgl_awal');
            $tglAkhir = $request->input('tgl_akhir');

            $barang->whereBetween('tanggal', [
                $tglAwal.' 00:00:00',
                $tglAkhir.' 23:59:59',
            ]);
        }

        $dataBarang = $barang->paginate(5)->withQueryString();

        return view('dashboard', compact(
            'jumlahBarang',
            'jumlahMahasiswa',
            'jumlahPegawai',
            'jumlahAkun',
            'dataBarang',
            'tglAwal',
            'tglAkhir',
        ));
    }
}
