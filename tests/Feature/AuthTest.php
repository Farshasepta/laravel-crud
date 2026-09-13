<?php

namespace Tests\Feature;

use App\Models\Akun;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Login');
    }

    public function test_admin_can_login_and_see_dashboard(): void
    {
        Http::fake([
            'google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
        ]);

        // inisialisasi session supaya CSRF token tersedia
        $this->get('/login');

        $response = $this->post('/login', [
            '_token' => csrf_token(),
            'username' => 'admin',
            'password' => 'admin',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(Akun::where('username', 'admin')->first());
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
