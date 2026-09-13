<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Barang;
use App\Models\Mahasiswa;
use App\Models\Pegawai;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeatureTest extends TestCase
{
    private function loginAsAdmin()
    {
        $admin = Akun::where('level', '1')->first();

        return $this->actingAs($admin, 'web');
    }

    private function loginAsOperatorBarang()
    {
        $op = Akun::where('level', '2')->first();

        return $this->actingAs($op, 'web');
    }

    private function loginAsOperatorMahasiswa()
    {
        $op = Akun::where('level', '3')->first();

        return $this->actingAs($op, 'web');
    }

    // ==================== AUTH ====================

    public function test_login_with_wrong_recaptcha_fails(): void
    {
        Http::fake([
            'google.com/recaptcha/api/siteverify' => Http::response(['success' => false]),
        ]);

        $this->get('/login');

        $this->post('/login', [
            '_token' => csrf_token(),
            'username' => 'admin',
            'password' => 'admin',
            'g-recaptcha-response' => 'invalid',
        ])->assertSessionHasErrors('recaptcha');
    }

    public function test_login_with_wrong_password_fails(): void
    {
        Http::fake([
            'google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
        ]);

        $this->get('/login');

        $this->post('/login', [
            '_token' => csrf_token(),
            'username' => 'admin',
            'password' => 'wrongpassword',
            'g-recaptcha-response' => 'valid',
        ])->assertSessionHasErrors('login');
    }

    public function test_login_skips_recaptcha_when_secret_key_empty(): void
    {
        config(['services.recaptcha.secret_key' => null]);
        config(['services.recaptcha.site_key' => null]);

        $this->get('/login');

        $this->post('/login', [
            '_token' => csrf_token(),
            'username' => 'admin',
            'password' => 'admin',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_logout_works(): void
    {
        $this->loginAsAdmin()
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // ==================== BARANG CRUD ====================

    public function test_barang_can_be_created(): void
    {
        $this->loginAsAdmin()
            ->post('/barang', [
                'nama' => 'Test Barang',
                'jumlah' => 5,
                'harga' => 15000,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('barang', [
            'nama' => 'Test Barang',
            'jumlah' => 5,
            'harga' => 15000,
        ]);
    }

    public function test_barang_create_generates_barcode(): void
    {
        $barang = Barang::where('nama', 'Test Barang')->first();
        $this->assertNotNull($barang);
        $this->assertGreaterThanOrEqual(100000, $barang->barcode);
        $this->assertLessThanOrEqual(999999, $barang->barcode);
    }

    public function test_barang_validation_rejects_zero_jumlah(): void
    {
        $this->loginAsAdmin()
            ->post('/barang', [
                'nama' => 'Test Gagal',
                'jumlah' => 0,
                'harga' => 1000,
            ])
            ->assertSessionHasErrors('jumlah');
    }

    public function test_barang_can_be_updated(): void
    {
        $barang = Barang::where('nama', 'Test Barang')->first();
        $this->assertNotNull($barang);

        $this->loginAsAdmin()
            ->put('/barang/'.$barang->id_barang, [
                'nama' => 'Test Barang Update',
                'jumlah' => 10,
                'harga' => 20000,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('barang', [
            'id_barang' => $barang->id_barang,
            'nama' => 'Test Barang Update',
            'jumlah' => 10,
            'harga' => 20000,
        ]);
    }

    public function test_barang_can_be_deleted(): void
    {
        $barang = Barang::where('nama', 'Test Barang Update')->first();
        $this->assertNotNull($barang);

        $this->loginAsAdmin()
            ->delete('/barang/'.$barang->id_barang)
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('barang', ['id_barang' => $barang->id_barang]);
    }

    public function test_dashboard_date_filter_works(): void
    {
        $this->loginAsAdmin()
            ->get('/dashboard?filter=1&tgl_awal=2020-01-01&tgl_akhir=2030-12-31')
            ->assertOk();
    }

    public function test_dashboard_date_filter_with_partial_dates_does_not_crash(): void
    {
        // hanya tgl_awal diisi -> tidak boleh error
        $this->loginAsAdmin()
            ->get('/dashboard?filter=1&tgl_awal=2020-01-01')
            ->assertOk();

        // hanya tgl_akhir diisi -> tidak boleh error
        $this->loginAsAdmin()
            ->get('/dashboard?filter=1&tgl_akhir=2030-12-31')
            ->assertOk();
    }

    // ==================== MAHASISWA CRUD ====================

    public function test_mahasiswa_can_be_created_with_photo(): void
    {
        $this->loginAsAdmin()
            ->post('/mahasiswa', [
                'nama' => 'Test Mahasiswa',
                'prodi' => 'Teknik Informatika',
                'jk' => 'Laki-Laki',
                'telepon' => '08123456789',
                'alamat' => 'Jl. Test No. 1',
                'email' => 'testmhs@example.com',
                'foto' => UploadedFile::fake()->image('foto.jpg', 100, 100),
            ])
            ->assertRedirect(route('mahasiswa.index'));

        $mahasiswa = Mahasiswa::where('email', 'testmhs@example.com')->first();
        $this->assertNotNull($mahasiswa);
        $this->assertNotNull($mahasiswa->foto);
        $this->assertFileExists(public_path('assets/img/'.$mahasiswa->foto));
    }

    public function test_mahasiswa_can_be_updated_without_photo(): void
    {
        $mahasiswa = Mahasiswa::where('email', 'testmhs@example.com')->first();
        $this->assertNotNull($mahasiswa);

        $this->loginAsAdmin()
            ->put('/mahasiswa/'.$mahasiswa->id_mahasiswa, [
                'nama' => 'Test Mahasiswa Update',
                'prodi' => 'Teknik Mesin',
                'jk' => 'Perempuan',
                'telepon' => '08129998877',
                'alamat' => 'Jl. Test No. 2',
                'email' => 'testmhs@example.com',
            ])
            ->assertRedirect(route('mahasiswa.index'));

        $this->assertDatabaseHas('mahasiswa', [
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'nama' => 'Test Mahasiswa Update',
        ]);

        // foto lama tetap ada
        $mahasiswa->refresh();
        $this->assertFileExists(public_path('assets/img/'.$mahasiswa->foto));
    }

    public function test_mahasiswa_show_page(): void
    {
        $mahasiswa = Mahasiswa::where('email', 'testmhs@example.com')->first();
        $this->assertNotNull($mahasiswa);

        $this->loginAsAdmin()
            ->get('/mahasiswa/'.$mahasiswa->id_mahasiswa)
            ->assertOk()
            ->assertSee($mahasiswa->nama);
    }

    public function test_mahasiswa_can_be_deleted_and_photo_removed(): void
    {
        $mahasiswa = Mahasiswa::where('email', 'testmhs@example.com')->first();
        $this->assertNotNull($mahasiswa);

        $fotoPath = public_path('assets/img/'.$mahasiswa->foto);

        $this->loginAsAdmin()
            ->delete('/mahasiswa/'.$mahasiswa->id_mahasiswa)
            ->assertRedirect(route('mahasiswa.index'));

        $this->assertDatabaseMissing('mahasiswa', ['id_mahasiswa' => $mahasiswa->id_mahasiswa]);
        $this->assertFileDoesNotExist($fotoPath);
    }

    // ==================== PEGAWAI CRUD ====================

    public function test_pegawai_can_be_created(): void
    {
        $this->loginAsAdmin()
            ->post('/pegawai', [
                'nama' => 'Test Pegawai',
                'jabatan' => 'Staf IT',
                'email' => 'pegawaitest@example.com',
                'telepon' => '085712345678',
                'alamat' => 'Jl. Pegawai No. 1',
                'tanggal_masuk' => '2024-01-15',
            ])
            ->assertRedirect(route('pegawai.index'));

        $this->assertDatabaseHas('pegawai', [
            'nama' => 'Test Pegawai',
            'jabatan' => 'Staf IT',
        ]);
    }

    public function test_pegawai_filter_by_nama_and_jabatan(): void
    {
        $this->loginAsAdmin()
            ->get('/pegawai?nama=Test&jabatan=IT')
            ->assertOk()
            ->assertSee('Test Pegawai');
    }

    public function test_pegawai_can_be_updated(): void
    {
        $pegawai = Pegawai::where('nama', 'Test Pegawai')->first();
        $this->assertNotNull($pegawai);

        $this->loginAsAdmin()
            ->put('/pegawai/'.$pegawai->id_pegawai, [
                'nama' => 'Test Pegawai Update',
                'jabatan' => 'Kepala IT',
                'email' => 'pegawaitest@example.com',
                'telepon' => '085712345678',
                'alamat' => 'Jl. Pegawai No. 2',
                'tanggal_masuk' => '2024-02-20',
            ])
            ->assertRedirect(route('pegawai.index'));

        $this->assertDatabaseHas('pegawai', [
            'id_pegawai' => $pegawai->id_pegawai,
            'nama' => 'Test Pegawai Update',
            'jabatan' => 'Kepala IT',
        ]);
    }

    public function test_pegawai_can_be_deleted(): void
    {
        $pegawai = Pegawai::where('nama', 'Test Pegawai Update')->first();
        $this->assertNotNull($pegawai);

        $this->loginAsAdmin()
            ->delete('/pegawai/'.$pegawai->id_pegawai)
            ->assertRedirect(route('pegawai.index'));

        $this->assertDatabaseMissing('pegawai', ['id_pegawai' => $pegawai->id_pegawai]);
    }

    // ==================== AKUN CRUD ====================

    public function test_admin_can_create_akun(): void
    {
        $this->loginAsAdmin()
            ->post('/akun', [
                'nama' => 'Test Operator',
                'username' => 'testoperator',
                'email' => 'testop@example.com',
                'password' => 'secret123',
                'level' => '2',
            ])
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('akun', [
            'username' => 'testoperator',
            'level' => '2',
        ]);
    }

    public function test_akun_password_is_hashed(): void
    {
        $akun = Akun::where('username', 'testoperator')->first();
        $this->assertNotNull($akun);
        $this->assertNotEquals('secret123', $akun->password);
        $this->assertTrue(password_verify('secret123', $akun->password));
    }

    public function test_admin_can_update_akun(): void
    {
        $akun = Akun::where('username', 'testoperator')->first();
        $this->assertNotNull($akun);

        $this->loginAsAdmin()
            ->post('/akun/'.$akun->id_akun, [
                'nama' => 'Test Operator Update',
                'username' => 'testoperator',
                'email' => 'testop@example.com',
                'password' => '',
                'level' => '3',
            ])
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('akun', [
            'id_akun' => $akun->id_akun,
            'nama' => 'Test Operator Update',
            'level' => '3',
        ]);
    }

    public function test_non_admin_cannot_change_level(): void
    {
        $akun = Akun::where('username', 'testoperator')->first();
        $this->assertNotNull($akun);

        $this->loginAsOperatorMahasiswa()
            ->post('/akun/'.$akun->id_akun, [
                'nama' => 'Test Operator Update 2',
                'username' => 'testoperator',
                'email' => 'testop@example.com',
                'password' => '',
                'level' => '1',
            ]);

        $akun->refresh();
        $this->assertEquals('3', (string) $akun->level);
    }

    public function test_admin_can_delete_akun(): void
    {
        $akun = Akun::where('username', 'testoperator')->first();
        $this->assertNotNull($akun);

        $this->loginAsAdmin()
            ->delete('/akun/'.$akun->id_akun)
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseMissing('akun', ['id_akun' => $akun->id_akun]);
    }

    public function test_akun_index_hides_other_accounts_from_non_admin(): void
    {
        $response = $this->loginAsOperatorBarang()->get('/akun')->assertRedirect(route('dashboard'));
        $this->assertNotNull($response);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = Akun::where('level', '1')->first();
        $this->assertNotNull($admin);

        $this->loginAsAdmin()
            ->delete('/akun/'.$admin->id_akun)
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('akun', ['id_akun' => $admin->id_akun]);
    }

    public function test_admin_cannot_delete_last_admin(): void
    {
        // buat akun admin kedua lalu hapus satu, sisakan satu -> coba hapus yang tersisa
        $this->loginAsAdmin()
            ->post('/akun', [
                'nama' => 'Admin 2',
                'username' => 'admin2',
                'email' => 'admin2@example.com',
                'password' => 'secret123',
                'level' => '1',
            ]);

        $admin2 = Akun::where('username', 'admin2')->first();
        $this->assertNotNull($admin2);

        // hapus admin2 dulu (masih ada 1 admin lain, seharusnya boleh)
        $this->loginAsAdmin()
            ->delete('/akun/'.$admin2->id_akun)
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseMissing('akun', ['id_akun' => $admin2->id_akun]);
    }

    // ==================== ROLE ACCESS ====================

    public function test_operator_barang_cannot_access_akun_or_mahasiswa(): void
    {
        $this->loginAsOperatorBarang()->get('/akun')->assertRedirect(route('dashboard'));
        $this->loginAsOperatorBarang()->get('/mahasiswa')->assertRedirect(route('dashboard'));
        $this->loginAsOperatorBarang()->get('/mahasiswa/create')->assertRedirect(route('dashboard'));
    }

    public function test_operator_mahasiswa_cannot_access_barang_create(): void
    {
        $this->loginAsOperatorMahasiswa()->get('/barang/create')->assertRedirect(route('dashboard'));
        $this->loginAsOperatorMahasiswa()->post('/barang', [
            'nama' => 'X',
            'jumlah' => 1,
            'harga' => 1,
        ])->assertRedirect(route('dashboard'));
    }

    public function test_guest_cannot_access_all_pages(): void
    {
        $pages = ['/dashboard', '/barang/create', '/mahasiswa', '/pegawai', '/email', '/akun'];
        foreach ($pages as $page) {
            $this->get($page)->assertRedirect(route('login'));
        }
    }

    // ==================== EXPORT ====================

    public function test_excel_export_contains_data(): void
    {
        $mahasiswa = Mahasiswa::first();
        $this->assertNotNull($mahasiswa);

        $this->loginAsAdmin()
            ->get('/mahasiswa/export/excel')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_pdf_export_contains_data(): void
    {
        $this->loginAsAdmin()
            ->get('/mahasiswa/export/pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    // ==================== BARCODE ====================

    public function test_barcode_endpoint_returns_png(): void
    {
        $this->get('/barcode?text=123456&codetype=Code128&size=15&print=true')
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_barcode_endpoint_works_without_auth(): void
    {
        $this->get('/barcode?text=ABC&codetype=Code128A')
            ->assertOk();
    }

    // ==================== EMAIL ====================

    public function test_email_form_page_renders(): void
    {
        $this->loginAsAdmin()
            ->get('/email')
            ->assertOk()
            ->assertSee('Kirim Email');
    }

    public function test_email_send_works(): void
    {
        // gunakan mailer log supaya email tidak benar-benar terkirim saat test
        config(['mail.default' => 'log']);

        $this->loginAsAdmin()
            ->post('/email', [
                'email_penerima' => 'test@example.com',
                'subject' => 'Test Subject',
                'pesan' => 'Test Pesan',
            ])
            ->assertRedirect(route('email.index'));
    }

    public function test_email_validation_rejects_invalid_recipient(): void
    {
        $this->loginAsAdmin()
            ->post('/email', [
                'email_penerima' => 'invalid-email',
                'subject' => 'Test',
                'pesan' => 'Test',
            ])
            ->assertSessionHasErrors('email_penerima');
    }

    public function test_operator_mahasiswa_can_access_email(): void
    {
        $this->loginAsOperatorMahasiswa()
            ->get('/email')
            ->assertOk();
    }

    public function test_operator_barang_cannot_access_email(): void
    {
        $this->loginAsOperatorBarang()
            ->get('/email')
            ->assertRedirect(route('dashboard'));
    }
}
