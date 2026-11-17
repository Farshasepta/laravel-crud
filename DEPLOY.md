# Panduan Deploy Ulang — Sistem CRUD + Portofolio (Laravel 12)

Dokumen ini menggantikan catatan deploy lama. Ringkasan kondisinya:

- Aplikasi ini dulu jalan di `farshalaravel.free.nf` (InfinityFree) dengan struktur **CRUD di root**, portofolio sebagai Blade (`resources/views/portofolio.blade.php`).
- Subdomain `free.nf`-nya sekarang sudah tidak terdaftar di akun hosting, sehingga request berhenti di halaman *"DNS Resolution Error – Domain Not Found"* dari InfinityFree. Itu error sisi hosting, bukan error Laravel.
- Source lengkap ada di repo ini. Gambar portofolio juga sudah ikut di `public/portfolio-assets/`.

Yang dibutuhkan server:

- PHP **8.2+** dengan ekstensi `pdo_mysql`, `gd`, `zip`, `dom`, `mbstring`, `openssl`, `curl`
- MySQL / MariaDB
- Document root mengarah ke `public/` (atau ikuti trik `server.php` untuk InfinityFree)

---

## 1. Pilih target hosting

| Opsi | Biaya | Kelebihan | Kekurangan |
|------|-------|-----------|------------|
| **InfinityFree** | Gratis | Tanpa kartu, PHP 8.2/8.3, MySQL | Tanpa SSH/Composer, upload `vendor/` manual, batas 10 MB per file, akun bisa dinonaktifkan jika lama tidak login |
| **cPanel / shared hosting** (Hostinger, Rumahweb, dll) | Berbayar | Ada SSH + Composer, setup cepat, SSL otomatis | Perlu biaya |
| **VPS** (Contabo, DigitalOcean, dll) | Berbayar | Kontrol penuh, paling stabil | Perlu setup Nginx + PHP-FPM + MySQL |

Kalau hanya butuh hidup lagi tanpa biaya, pakai **InfinityFree** (bagian 3). Untuk jangka panjang, cPanel/VPS lebih direkomendasikan.

---

## 2. Persiapan di komputer lokal

Hosting tanpa SSH tidak bisa menjalankan Composer/Artisan, jadi semua dependensi dan database disiapkan lokal.

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --show   # simpan hasilnya untuk APP_KEY
```

Siapkan database dan dump SQL:

```bash
cp .env.example .env
# isi DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD lokal
php artisan migrate --seed
mysqldump -u root -p crud_php > crud_php.sql
```

Akun default dari seeder:

- Admin: `admin` / `admin`
- Operator: `operator` / `operator`

**Ganti password ini setelah deploy.**

Catatan: repo ini tidak memakai Vite/Tailwind di view (semua CSS/JS lewat `public/assets` dan `public/assets-template`), jadi `npm run build` **tidak diperlukan**.

---

## 3. Deploy ke InfinityFree

### 3.1 Akun dan subdomain

1. Login ke client area InfinityFree.
2. Cek akun lama:
   - Jika status **Suspended / Inactive** → klik reaktivasi, tunggu sampai **Active**.
   - Jika akun sudah dihapus → buat akun baru, lalu tentukan subdomain baru (mis. `farshalaravel2.free.nf`).
3. Tunggu DNS aktif (biasanya beberapa menit, maksimal 24 jam).

### 3.2 Buat database

1. Control Panel → **MySQL Databases** → Create Database.
2. Catat: `DB_HOST` (`sqlXXX.infinityfree.com`), `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
3. Buka **phpMyAdmin** → pilih database → tab **Import** → upload `crud_php.sql`.
   - Jika file > 10 MB, pecah dump per tabel atau gunakan BigDump.

### 3.3 Upload file

Struktur di server:

```
htdocs/
├── index.php              <- diisi require server.php
├── .htaccess              <- dari public/.htaccess
├── assets/                <- dari public/assets/
├── assets-template/       <- dari public/assets-template/
├── css/                   <- dari public/css/
├── favicon.ico
├── robots.txt
├── portfolio-assets/      <- dari public/portfolio-assets/
└── laravel-crud/          <- seluruh project (app, bootstrap, config, database,
                              public, resources, routes, storage, vendor, server.php, .env)
```

Langkah:

1. Upload seluruh project ke `htdocs/laravel-crud/` via FTP (FileZilla).
   - `vendor/` **wajib** ikut karena Composer tidak tersedia di server.
   - Batas 10 MB per file tidak masalah untuk file aplikasi, tapi `vendor/` terdiri dari ribuan file kecil — upload lewat FTP, jangan File Manager.
2. Salin isi `laravel-crud/public/` ke `htdocs/`: `assets`, `assets-template`, `css`, `favicon.ico`, `robots.txt`, `portfolio-assets`, `.htaccess`.
3. Ganti `htdocs/index.php` menjadi:

```php
<?php
require __DIR__.'/laravel-crud/server.php';
```

`server.php` sudah ada di repo dan mengarahkan bootstrap ke `laravel-crud/public/index.php`.

4. Buat `htdocs/laravel-crud/.env` (File Manager tidak bisa membuat dotfile: buat `env.txt`, lalu rename; atau upload via FTP).

### 3.4 Isi `.env` production

```env
APP_NAME="Sistem CRUD"
APP_ENV=production
APP_KEY=base64:...            # hasil php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://subdomain-baru.free.nf

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_xxxx_crud
DB_USERNAME=if0_xxxx
DB_PASSWORD=password_database

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack
LOG_LEVEL=error

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=akun@gmail.com
MAIL_PASSWORD=app_password_gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=akun@gmail.com
MAIL_FROM_NAME="Sistem CRUD"

RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

`RECAPTCHA_*` boleh kosong — login tetap jalan tanpa captcha.

Di Control Panel → **PHP Settings**, pilih PHP **8.2** atau **8.3** dan pastikan ekstensi `pdo_mysql`, `gd`, `zip`, `dom`, `mbstring` aktif.

### 3.5 Permission folder

Lewat File Manager/FTP, set `storage/` dan `bootstrap/cache/` (beserta subfoldernya, terutama `storage/framework/sessions`, `storage/framework/views`, `storage/framework/cache`, `storage/logs`) menjadi **755** (atau **775** bila 755 gagal). Tanpa ini akan muncul error 500.

### 3.6 Migrasi tanpa SSH (opsional)

Kalau tidak mau import SQL manual, buat file sementara `htdocs/migrate.php`:

```php
<?php
require __DIR__.'/laravel-crud/vendor/autoload.php';
$app = require_once __DIR__.'/laravel-crud/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', ['--force' => true]);
$kernel->call('db:seed', ['--force' => true]);
echo 'Migrasi selesai';
```

Buka `https://subdomain-baru.free.nf/migrate.php`, lalu **hapus file itu** setelah berhasil.

### 3.7 Aktifkan SSL

Control Panel → **SSL Certificates** → request sertifikat gratis untuk domain tersebut. Setelah aktif, paksa HTTPS (bisa lewat `.htaccess` atau menu hosting).

### 3.8 Verifikasi

- [ ] `https://subdomain-baru.free.nf/` menampilkan portofolio (foto profil dan 3 project card muncul)
- [ ] Tombol **Login** mengarah ke `/login`
- [ ] Login `admin` / `admin` berhasil → masuk `/dashboard`
- [ ] CRUD barang/mahasiswa/pegawai/akun bisa dibuka
- [ ] Export Excel/PDF dan halaman barcode tidak error
- [ ] Tidak ada pesan debug/stack trace (artinya `APP_DEBUG=false`)

---

## 4. Deploy ke VPS / cPanel (rekomendasi)

### VPS (Ubuntu + Nginx + PHP-FPM + MySQL)

```bash
sudo apt install nginx mysql-server php8.3-fpm php8.3-mysql php8.3-gd \
  php8.3-zip php8.3-mbstring php8.3-xml php8.3-curl unzip git
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer

cd /var/www && sudo git clone https://github.com/Farshasepta/laravel-crud.git
cd laravel-crud
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# isi DB_* di .env, buat database MySQL
php artisan migrate --seed --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

Konfigurasi Nginx minimal:

```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/laravel-crud/public;

    index index.php;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    location ~ /\.(?!well-known).* { deny all; }
}
```

Lalu `certbot --nginx -d domain-anda.com` untuk HTTPS.

### cPanel

1. Upload repo/zip ke `~/laravel-crud` (di luar `public_html`).
2. SSH: `composer install --no-dev --optimize-autoloader`, `php artisan key:generate`, `php artisan migrate --seed --force`.
3. Set document root domain ke `laravel-crud/public` (atau salin isi `public/` ke `public_html` dan ubah `index.php` mengikuti pola `server.php`).
4. Aktifkan AutoSSL.

---

## 5. Troubleshooting

| Gejala | Penyebab umum | Solusi |
|--------|---------------|--------|
| Halaman putih / 500 | `storage` atau `bootstrap/cache` tidak writable | Set permission 755/775 seperti bagian 3.5 |
| "Class not found" | `vendor/` tidak lengkap | Upload ulang `vendor/` hasil `composer install --no-dev` |
| Asset/CSS tidak muncul | Isi `public/` belum disalin ke `htdocs/` | Salin `assets`, `css`, `assets-template`, `portfolio-assets` |
| Portofolio tampil tapi gambar kosong | Folder `portfolio-assets/` belum ada di root | Upload `public/portfolio-assets/` → `htdocs/portfolio-assets/` |
| 404 di `/login` atau `/dashboard` | `.htaccess`/rewrite tidak aktif | Pastikan `.htaccess` dari `public/` ada di root dan `mod_rewrite` aktif |
| Error 1071 "key too long" | Versi MySQL lama + index unik panjang | Pakai database baru, jalankan ulang migrasi |
| "No application encryption key" | `APP_KEY` kosong | Isi hasil `php artisan key:generate --show` |
| Login selalu gagal | Session tidak bisa ditulis / captcha wajib | Cek permission `storage/framework/sessions`, kosongkan `RECAPTCHA_*` |
| Email tidak terkirim | Gmail menolak password biasa | Gunakan **App Password** Gmail dan `MAIL_ENCRYPTION=tls` |
| `farshalaravel.free.nf` DNS error | Subdomain sudah dilepas InfinityFree | Pakai subdomain baru dari akun aktif (bagian 3.1) |
