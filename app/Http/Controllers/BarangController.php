<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'integer', 'min:1'],
        ]);

        Barang::create([
            'nama' => $validated['nama'],
            'jumlah' => $validated['jumlah'],
            'harga' => $validated['harga'],
            'barcode' => rand(100000, 999999),
            'tanggal' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Data Barang Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);

        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'integer', 'min:1'],
        ]);

        Barang::where('id_barang', $id)->update($validated);

        return redirect()->route('dashboard')->with('success', 'Data Barang Berhasil Diubah');
    }

    public function destroy($id)
    {
        Barang::where('id_barang', $id)->delete();

        return redirect()->route('dashboard')->with('success', 'Data Barang Berhasil Dihapus');
    }
}
