<?php

namespace Tests\Feature;

use App\Models\Akun;
use Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_operator_barang_cannot_access_mahasiswa(): void
    {
        $op = Akun::where('level', '2')->first();
        $this->actingAs($op, 'web')->get('/mahasiswa')->assertRedirect(route('dashboard'));
    }

    public function test_operator_barang_can_access_dashboard(): void
    {
        $op = Akun::where('level', '2')->first();
        $this->actingAs($op, 'web')->get('/dashboard')->assertOk();
    }

    public function test_operator_mahasiswa_cannot_access_akun(): void
    {
        $op = Akun::where('level', '3')->first();
        $this->actingAs($op, 'web')->get('/akun')->assertRedirect(route('dashboard'));
    }

    public function test_operator_mahasiswa_can_access_mahasiswa(): void
    {
        $op = Akun::where('level', '3')->first();
        $this->actingAs($op, 'web')->get('/mahasiswa')->assertOk();
    }
}
