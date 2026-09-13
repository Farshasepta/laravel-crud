<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        $dataMahasiswa = Mahasiswa::orderByDesc('id_mahasiswa')->get();

        $prodiData = Mahasiswa::query()
            ->selectRaw('prodi, COUNT(*) as jumlah')
            ->groupBy('prodi')
            ->orderByDesc('jumlah')
            ->get();

        $jkData = Mahasiswa::query()
            ->selectRaw('jk, COUNT(*) as jumlah')
            ->groupBy('jk')
            ->get();

        return view('mahasiswa.index', compact('dataMahasiswa', 'prodiData', 'jkData'));
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'prodi' => ['required', 'string'],
            'jk' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'email' => ['required', 'email', 'max:30'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $validated['foto'] = $this->uploadFoto($request);

        Mahasiswa::create($validated);

        return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa Berhasil Ditambahkan');
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        return view('mahasiswa.show', compact('mahasiswa'));
    }

    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'prodi' => ['required', 'string'],
            'jk' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'email' => ['required', 'email', 'max:30'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $validated['foto'] = $this->uploadFoto($request)
            ?? $mahasiswa->foto;

        $mahasiswa->update($validated);

        return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa Berhasil Diubah');
    }

    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $fotoPath = public_path('assets/img/'.$mahasiswa->foto);
        if ($mahasiswa->foto && file_exists($fotoPath)) {
            unlink($fotoPath);
        }

        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data Mahasiswa Berhasil Dihapus');
    }

    private function uploadFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        $file = $request->file('foto');
        $namaFile = uniqid().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('assets/img'), $namaFile);

        return $namaFile;
    }
}
