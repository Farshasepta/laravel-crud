<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index()
    {
        $isAdmin = Auth::user()->level == 1;

        $dataAkun = $isAdmin
            ? Akun::all()
            : Akun::where('id_akun', Auth::id())->get();

        return view('akun.index', compact('dataAkun', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:akun,username'],
            'email' => ['required', 'email', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
            'level' => ['required', 'in:1,2,3'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Akun::create($validated);

        return redirect()->route('akun.index')->with('success', 'Data Akun Berhasil Ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);

        $isAdmin = Auth::user()->level == 1;

        // non-admin hanya boleh mengubah akunnya sendiri
        if (! $isAdmin && Auth::id() != $akun->id_akun) {
            return redirect()->route('akun.index')->with('error', 'Anda tidak punya hak akses');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:akun,username,'.$akun->id_akun.',id_akun'],
            'email' => ['required', 'email', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
            'level' => ['required', 'in:1,2,3'],
        ]);

        if (! $isAdmin) {
            $validated['level'] = $akun->level;
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $akun->update($validated);

        return redirect()->route('akun.index')->with('success', 'Data Akun Berhasil Diubah');
    }

    public function destroy($id)
    {
        $akun = Akun::findOrFail($id);

        // admin tidak boleh menghapus dirinya sendiri
        if ($akun->id_akun === Auth::id()) {
            return redirect()->route('akun.index')->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        // cegah menghapus admin terakhir
        if ($akun->level == '1' && Akun::where('level', '1')->count() <= 1) {
            return redirect()->route('akun.index')->with('error', 'Tidak dapat menghapus admin terakhir');
        }

        $akun->delete();

        return redirect()->route('akun.index')->with('success', 'Data Akun Berhasil Dihapus');
    }
}
