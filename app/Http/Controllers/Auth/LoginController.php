<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:72'],
        ];

        // reCAPTCHA hanya wajib jika secret key sudah dikonfigurasi
        if (config('services.recaptcha.secret_key')) {
            $rules['g-recaptcha-response'] = ['required'];
        }

        $credentials = $request->validate($rules);

        // Lewati verifikasi reCAPTCHA jika secret key belum diisi
        // (berguna saat development/tanpa key; di production key wajib terisi)
        if (config('services.recaptcha.secret_key')) {
            $recaptcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
            ])->json();

            if (! ($recaptcha['success'] ?? false)) {
                return back()->withErrors([
                    'recaptcha' => 'Recaptcha Tidak Valid',
                ])->withInput();
            }
        }

        $akun = Akun::where('username', $credentials['username'])->first();

        if (! $akun || ! Hash::check($credentials['password'], $akun->password)) {
            return back()->withErrors([
                'login' => 'Username/Password SALAH',
            ])->withInput();
        }

        Auth::login($akun);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
