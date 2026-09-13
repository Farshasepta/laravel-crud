<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\Barang;
use App\Models\Mahasiswa;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Data awal untuk aplikasi Sistem CRUD.
     *
     * Akun default:
     * - Admin        : admin / admin
     * - Operator     : operator / operator
     */
    public function run(): void
    {
        if (Akun::count() === 0) {
            Akun::create([
                'nama' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@email.com',
                'password' => Hash::make('admin'),
                'level' => '1',
            ]);

            Akun::create([
                'nama' => 'Operator',
                'username' => 'operator',
                'email' => 'operator@email.com',
                'password' => Hash::make('operator'),
                'level' => '2',
            ]);
        }

        if (Barang::count() === 0) {
            Barang::create([
                'nama' => 'Laptop ASUS ROG',
                'jumlah' => 10,
                'harga' => 15000000,
                'barcode' => '123456',
                'tanggal' => now(),
            ]);

            Barang::create([
                'nama' => 'Printer Epson L3210',
                'jumlah' => 5,
                'harga' => 2500000,
                'barcode' => '654321',
                'tanggal' => now(),
            ]);
        }

        if (Mahasiswa::count() === 0) {
            Mahasiswa::create([
                'nama' => 'Andi Saputra',
                'prodi' => 'Teknik Informatika',
                'jk' => 'Laki-Laki',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1',
                'email' => 'andi@example.com',
                'foto' => '',
            ]);

            Mahasiswa::create([
                'nama' => 'Siti Rahayu',
                'prodi' => 'Teknik Mesin',
                'jk' => 'Perempuan',
                'telepon' => '081298765432',
                'alamat' => 'Jl. Sudirman No. 2',
                'email' => 'siti@example.com',
                'foto' => '',
            ]);
        }

        if (Pegawai::count() === 0) {
            Pegawai::create([
                'nama' => 'Budi Hartono',
                'jabatan' => 'Direktur',
                'email' => 'budi@example.com',
                'telepon' => '081312345678',
                'alamat' => 'Jl. Gatot Subroto No. 3',
                'tanggal_masuk' => '2020-01-01',
            ]);

            Pegawai::create([
                'nama' => 'Dewi Lestari',
                'jabatan' => 'Wakil Direktur',
                'email' => 'dewi@example.com',
                'telepon' => '081399988877',
                'alamat' => 'Jl. Ahmad Yani No. 4',
                'tanggal_masuk' => '2021-06-15',
            ]);
        }
    }
}
