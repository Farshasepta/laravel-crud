<?php

namespace Tests\Feature;

use App\Models\Akun;
use Tests\TestCase;

class CrudTest extends TestCase
{
    private function actingAsAdmin()
    {
        $admin = Akun::where('level', '1')->first();

        return $this->actingAs($admin, 'web');
    }

    public function test_admin_sees_barang_dashboard(): void
    {
        $this->actingAsAdmin()->get('/dashboard')->assertOk()->assertSee('Data Barang');
    }

    public function test_admin_sees_mahasiswa_index(): void
    {
        $this->actingAsAdmin()->get('/mahasiswa')->assertOk()->assertSee('Tabel Data Mahasiswa');
    }

    public function test_admin_sees_pegawai_index(): void
    {
        $this->actingAsAdmin()->get('/pegawai')->assertOk()->assertSee('Tabel Data Pegawai');
    }

    public function test_admin_sees_akun_index(): void
    {
        $this->actingAsAdmin()->get('/akun')->assertOk()->assertSee('Tabel Data Akun');
    }
}
