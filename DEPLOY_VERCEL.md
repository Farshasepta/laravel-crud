# Deploy ke Vercel (Container + FrankenPHP)

Vercel tidak punya runtime PHP native. Jalur resmi yang dipakai di sini adalah **Container Function**: Vercel membangun image dari `Dockerfile.vercel` (FrankenPHP + PHP 8.3), lalu menjalankannya sebagai function. File yang disiapkan:

- `Dockerfile.vercel` — image produksi (extensi `pdo_mysql`, `gd`, `zip`, `intl`, `bcmath`, `opcache`)
- `Caddyfile` — document root ke `public/`, listen di `$PORT` (default 80)
- `.dockerignore` — mengecualikan `vendor`, `.env`, cache lokal

Tanpa `vercel.json` pun Vercel otomatis memilih preset **Container** karena ada `Dockerfile.vercel`.

> Catatan penting: container Vercel **tidak punya disk permanen**. Folder deployment read-only, hanya `/tmp` yang writable dan bisa hilang kapan saja. Database, session, cache, dan file upload harus di layanan eksternal.

---

## 1. Yang harus disiapkan di luar Vercel

| Kebutuhan | Rekomendasi | Keterangan |
|-----------|-------------|------------|
| Database | MySQL eksternal (Aiven free MySQL, TiDB Cloud Serverless, Railway MySQL, atau cPanel/hosting lain) | Vercel hanya menyediakan Postgres/Redis di marketplace; aplikasi ini memakai MySQL |
| Upload foto mahasiswa | Cloudflare R2 / S3 / Vercel Blob | **Belum diimplementasikan** di kode — lihat bagian 5 |
| Session | `SESSION_DRIVER=cookie` | Tidak perlu tabel/Redis; cukup untuk app ini |
| Cache | `CACHE_STORE=array` | Per-request, aman karena tidak ada data kritis di cache |

Database harus bisa diakses dari internet (function Vercel tidak punya IP keluar tetap). Aktifkan TLS dan gunakan password kuat.

---

## 2. Siapkan database dan migrasi (dari komputer lokal)

Vercel tidak bisa menjalankan `php artisan migrate` saat request. Jalankan sekali dari lokal/CI dengan menunjuk ke database remote:

```bash
php artisan migrate --seed --force \
  --env=production
```

Atau lebih aman lewat variabel environment sementara:

```bash
DB_CONNECTION=mysql \
DB_HOST=xxx.db.example.com \
DB_PORT=3306 \
DB_DATABASE=nama_db \
DB_USERNAME=user \
DB_PASSWORD=password \
php artisan migrate --seed --force
```

Akun default dari seeder: `admin` / `admin` dan `operator` / `operator` — ganti setelah deploy.

---

## 3. Import repo ke Vercel

1. Buka <https://vercel.com/new> → import `Farshasepta/laravel-crud`.
2. Framework Preset: **Container** (terdeteksi otomatis dari `Dockerfile.vercel`). Root Directory: `.`
3. Tambahkan environment variables (Settings → Environment Variables) untuk Production.

### Environment variables wajib

```env
APP_NAME="Sistem CRUD"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nama-project.vercel.app
APP_KEY=base64:...            # php artisan key:generate --show

DB_CONNECTION=mysql
DB_HOST=xxx.db.example.com
DB_PORT=3306
DB_DATABASE=nama_db
DB_USERNAME=user
DB_PASSWORD=password

SESSION_DRIVER=cookie
CACHE_STORE=array
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
LOG_LEVEL=error

APP_CONFIG_CACHE=/tmp/config.php
APP_ROUTES_CACHE=/tmp/routes.php
APP_EVENTS_CACHE=/tmp/events.php
APP_PACKAGES_CACHE=/tmp/packages.php
APP_SERVICES_CACHE=/tmp/services.php
VIEW_COMPILED_PATH=/tmp
```

Penjelasan: container Vercel hanya mengizinkan tulis di `/tmp`, jadi semua cache bootstrap dan hasil kompilasi Blade diarahkan ke sana; log dikirim ke stderr agar muncul di Vercel Runtime Logs.

### Opsional (fitur email / captcha)

```env
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

`RECAPTCHA_*` kosong = login tanpa captcha.

---

## 4. Deploy

- Lewat dashboard: klik **Deploy**. Push ke branch produksi akan memicu build ulang.
- Lewat CLI: `npm i -g vercel` → `vercel login` → `vercel deploy --prod`.
- Build pertama lebih lama karena mengunduh image FrankenPHP dan `composer install`.

### Checklist setelah deploy

- [ ] `/` menampilkan portofolio (gambar `portfolio-assets/*` muncul)
- [ ] `/login` tampil dan login `admin`/`admin` berhasil
- [ ] `/dashboard`, CRUD barang/mahasiswa/pegawai/akun terbuka
- [ ] Export Excel/PDF berhasil (dibatasi durasi function)
- [ ] Reload beberapa kali tetap login → `SESSION_DRIVER=cookie` bekerja
- [ ] Tidak ada stack trace (`APP_DEBUG=false`)

---

## 5. Keterbatasan yang harus diketahui

| Fitur | Di Vercel | Solusi |
|-------|-----------|--------|
| Upload foto mahasiswa (`public/assets/img`) | **Tidak bisa** — folder `public` read-only dan `/tmp` tidak dibagikan antar instance | Simpan ke object storage (R2/S3/Vercel Blob) lalu tampilkan lewat URL storage |
| Session file | Hilang antar instance | `SESSION_DRIVER=cookie` (sudah disarankan) |
| Cache file | Tidak persist | `CACHE_STORE=array` atau Redis marketplace |
| Export besar | Function punya batas durasi | Batasi jumlah baris atau pindah ke worker |
| Queue worker / scheduler | Tidak ada proses long-running | App ini `QUEUE_CONNECTION=sync`; kalau butuh nanti pakai Vercel Cron/Daemons |
| Disk permanen | Tidak tersedia | Pakai storage eksternal |

Artinya: setelah deploy, halaman portofolio, login, dashboard, dan CRUD data berjalan normal; **hanya upload foto** yang butuh object storage. Kalau ingin upload tetap jalan tanpa layanan tambahan, hosting dengan disk permanen (Railway/Render/VPS/cPanel) lebih tepat.

---

## 6. Troubleshooting

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Build gagal di `composer install` | Ekstensi PHP kurang | Tambah di `Dockerfile.vercel` (`install-php-extensions ...`) |
| 502 saat dibuka | Port Caddy tidak cocok | Pastikan `Caddyfile` memakai `:{$PORT:80}` dan tidak set `PORT` lain di Vercel |
| 500 / blank | Tulis ke `bootstrap/cache` atau `storage` ditolak | Pastikan 5 variabel `APP_*_CACHE` + `VIEW_COMPILED_PATH` terpasang |
| "No application encryption key" | `APP_KEY` kosong | Isi dari `php artisan key:generate --show` |
| Logout sendiri tiap request | Session file/tidak konsisten | `SESSION_DRIVER=cookie` |
| `SQLSTATE[HY000] [2002]` | DB tidak reachable | Buka akses publik DB, cek host/port, aktifkan TLS |
| Gambar portofolio 404 | `public/portfolio-assets` tidak ikut image | Pastikan tidak ada aturan di `.dockerignore` yang mengecualikannya |
| Aset CSS/JS 404 | Caddy root salah | `root * /app/public` dan `php_server` |
