<?php

namespace Tests\Feature;

use App\Models\Akun;
use Tests\TestCase;

class ExportTest extends TestCase
{
    private function actingAsAdmin()
    {
        $admin = Akun::where('level', '1')->first();

        return $this->actingAs($admin, 'web');
    }

    public function test_excel_export_downloads(): void
    {
        $this->actingAsAdmin()->get('/mahasiswa/export/excel')->assertOk();
    }

    public function test_pdf_export_downloads(): void
    {
        $this->actingAsAdmin()->get('/mahasiswa/export/pdf')->assertOk();
    }
}
