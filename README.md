# Sistem CRUD

> Panduan deploy lengkap (InfinityFree, cPanel, VPS): lihat [DEPLOY.md](DEPLOY.md).
> Deploy ke Vercel (container/FrankenPHP): lihat [DEPLOY_VERCEL.md](DEPLOY_VERCEL.md).

Aplikasi CRUD berbasis **Laravel 12** dengan UI **AdminLTE 3 (Bootstrap 4)** untuk mengelola:
- **Barang** (dengan auto-generate barcode Code128 + filter tanggal)
- **Mahasiswa** (dengan upload foto + charts)
- **Pegawai** (dengan multi-filter + charts)
- **Akun** (manajemen user dengan role level)
- **Email** (kirim email via SMTP Gmail)
- **Export** (Excel & PDF untuk data mahasiswa)

## Fitur

- Login custom (username + password + Google reCAPTCHA v2)
- Role system: **Admin** (level 1), **Operator Barang** (level 2), **Operator Mahasiswa** (level 3)
- CRUD lengkap untuk barang, mahasiswa, pegawai, akun
- Barcode Code128 generator (tanpa library, GD)
- Export Excel (PhpSpreadsheet) & PDF (Html2Pdf)
- Kirim email (SMTP)
- Chart.js statistik

## Kebutuhan

- PHP >= 8.2
- Ekstensi: `pdo_mysql`, `gd`, `zip`, `dom`, `mbstring`, `openssl`, `curl`
- MySQL / MariaDB
- Composer

## Instalasi Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
# sesuaikan DB_* di .env
php artisan migrate --seed
php artisan serve
```

Akses `http://localhost:8000`.

### Akun Default (seeder)

| Level | Username | Password |
|-------|----------|----------|
| Admin | `admin` | `admin` |
| Operator Barang | `operator` | `operator` |

> **Segera ganti password setelah login pertama!**

## Testing

```bash
php artisan test
```

Suite mencakup auth, role access, seluruh CRUD, export, barcode, dan email.

## Deployment ke InfinityFree

Lihat [DEPLOY_INFINITYFREE.md](DEPLOY_INFINITYFREE.md) untuk panduan lengkap.

## Struktur

- `app/Http/Controllers/` - controller (Barang, Mahasiswa, Pegawai, Akun, Email, Export, Barcode, Auth)
- `app/Http/Middleware/CheckLevel.php` - middleware role level
- `app/Models/` - model Eloquent (akun, barang, mahasiswa, pegawai)
- `database/migrations/` - skema tabel
- `database/seeders/` - data awal
- `resources/views/` - Blade templates (AdminLTE)
- `tests/Feature/` - feature tests

## Lisensi

MIT
