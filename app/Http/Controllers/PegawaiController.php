<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = Pegawai::query()->orderByDesc('id_pegawai');

        $nama = $request->input('nama');
        $jabatan = $request->input('jabatan');
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        if ($nama) {
            $pegawai->where('nama', 'like', "%{$nama}%");
        }
        if ($jabatan) {
            $pegawai->where('jabatan', 'like', "%{$jabatan}%");
        }
        if ($tglAwal) {
            $pegawai->where('tanggal_masuk', '>=', $tglAwal);
        }
        if ($tglAkhir) {
            $pegawai->where('tanggal_masuk', '<=', $tglAkhir);
        }

        $dataPegawai = $pegawai->get();

        $jabatanData = Pegawai::query()
            ->selectRaw('jabatan, COUNT(*) as jumlah')
            ->groupBy('jabatan')
            ->orderByDesc('jumlah')
            ->get();

        return view('pegawai.index', compact(
            'dataPegawai',
            'jabatanData',
            'nama',
            'jabatan',
            'tglAwal',
            'tglAkhir',
        ));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'telepon' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'tanggal_masuk' => ['nullable', 'date'],
        ]);

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'telepon' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'tanggal_masuk' => ['nullable', 'date'],
        ]);

        Pegawai::where('id_pegawai', $id)->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai Berhasil Diubah');
    }

    public function destroy($id)
    {
        Pegawai::where('id_pegawai', $id)->delete();

        return redirect()->route('pegawai.index')->with('success', 'Data Pegawai Berhasil Dihapus');
    }
}
