# Sistem CRUD - Panduan Deploy ke InfinityFree

Aplikasi ini adalah **Laravel 12** dengan AdminLTE 3 (Bootstrap 4).

---

## Persyaratan Server (InfinityFree)

- PHP **8.2+** (InfinityFree mendukung PHP 8.x - pilih di Control Panel → PHP Settings)
- Ekstensi PHP yang wajib aktif:
  - `pdo_mysql`
  - `gd` (untuk gambar barcode)
  - `zip` (untuk export Excel)
  - `dom` (untuk export PDF)
  - `mbstring`
  - `openssl`, `curl`
- MySQL database (InfinityFree menyediakan 1 database gratis)

---

## Langkah Deploy

### 1. Siapkan file di komputer

1. Jalankan `composer install --no-dev --optimize-autoloader` untuk menghasilkan folder `vendor/` tanpa package dev.
2. Pastikan folder `storage/` dan `bootstrap/cache/` ikut ter-upload (folder ini harus writable di server).
3. Buat file `.env` di server (jangan upload `.env` lokal yang berisi credentials asli).

### 2. Upload ke InfinityFree

InfinityFree memakai `htdocs/` sebagai document root. Ada 2 cara:

#### Cara A (disarankan): Isi `public/` dijadikan document root

1. Upload seluruh project ke `htdocs/laravel-crud/` (misal).
2. Salin SEMUA isi folder `public/` (`assets`, `assets-template`, `css`, `favicon.ico`, `index.php`, `.htaccess`) ke `htdocs/` (document root).
3. Ganti isi `htdocs/index.php` (yang tadi disalin dari public/) menjadi:

```php
<?php
require __DIR__.'/laravel-crud/server.php';
```

File `server.php` sudah disiapkan di root project dan akan mem-bootstrap Laravel dari `public/index.php` di folder project.

#### Cara B: Semua di `htdocs/`

Upload seluruh isi project ke `htdocs/`, lalu merge isi `public/` ke atas. Pastikan `htdocs/index.php` adalah yang dari `public/index.php`.

### 3. Setup Database

1. Buat database di InfinityFree Control Panel → MySQL Databases.
2. Catat nama database, username, dan password.
3. Edit `.env` di server:

```env
APP_NAME="Sistem CRUD"
APP_ENV=production
APP_KEY=<generate: php artisan key:generate>
APP_DEBUG=false
APP_URL=https://namadomain.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_XXXXX_namadb
DB_USERNAME=if0_XXXXX
DB_PASSWORD=passwordnya

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailgmail@gmail.com
MAIL_PASSWORD=apppassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=emailgmail@gmail.com
MAIL_FROM_NAME="Admin"

RECAPTCHA_SITE_KEY=6Lc...sitekey
RECAPTCHA_SECRET_KEY=6Lc...secretkey
```

> **CATATAN reCAPTCHA:** Site key & secret key di `.env` lokal terikat ke domain tertentu. Untuk domain baru di InfinityFree, buat reCAPTCHA key baru di https://www.google.com/recaptcha/admin dengan menambahkan domain baru (misal `namadomain.infinityfreeapp.com`), lalu update `.env`.

> **CATATAN Gmail:** Untuk `MAIL_PASSWORD`, gunakan **App Password** Gmail (bukan password biasa) jika 2FA aktif. Aktivasi di https://myaccount.google.com/apppasswords

### 4. Migrasi & Seeder

Setelah upload, jalankan via SSH/Terminal (InfinityFree **tidak** menyediakan terminal publik; gunakan fitur **Cron Job** atau jalankan sekali lewat script):

```bash
php artisan migrate --seed --force
php artisan storage:link
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

Jika tidak ada akses terminal, buat file sementara `migrate.php` di root, akses via browser, lalu hapus:

```php
<?php
// migrate.php - HAPUS FILE INI SETELAH DIPAKAI!
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// Jalankan migration
Artisan::call('migrate', ['--seed' => true, '--force' => true]);
echo nl2br(Artisan::output());
```

### 5. Akun Default

Seeder membuat akun berikut:

| Level | Username | Password |
|-------|----------|----------|
| Admin | `admin` | `admin` |
| Operator Barang | `operator` | `operator` |

**Segera ganti password setelah login pertama!**

---

## Fitur yang Sudah Diuji

- ✅ Login/Logout + reCAPTCHA
- ✅ Role access (Admin = level 1, Operator Barang = level 2, Operator Mahasiswa = level 3)
- ✅ CRUD Barang + auto barcode + filter tanggal
- ✅ CRUD Mahasiswa + upload/hapus foto + charts
- ✅ CRUD Pegawai + multi-filter + charts
- ✅ CRUD Akun (proteksi admin terakhir & hapus diri sendiri)
- ✅ Export Excel & PDF
- ✅ Kirim Email (SMTP)
- ✅ Barcode Code128 (endpoint publik)

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| 500 error setelah upload | Cek `storage/logs/laravel.log`; pastikan `APP_KEY` ada & `storage/` writable |
| Halaman putih / blank | `php artisan config:clear` & `php artisan view:clear`; cek error log |
| Barcode tidak muncul | Pastikan ekstensi `gd` aktif; akses `/barcode?text=123` langsung |
| Export Excel gagal | Pastikan ekstensi `zip` aktif |
| Export PDF gagal | Pastikan ekstensi `dom` aktif |
| Email gagal kirim | Cek `MAIL_*` di `.env`; gunakan App Password Gmail |
| reCAPTCHA error | Domain baru butuh key baru di Google reCAPTCHA admin |
| Gambar/foto tidak muncul | Jalankan `php artisan storage:link` atau pastikan path `assets/img/` benar |
