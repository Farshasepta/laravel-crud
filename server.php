<?php

/**
 * Laravel - Fallback untuk shared hosting (InfinityFree, dll.)
 *
 * Document root InfinityFree adalah htdocs/, sementara Laravel butuh
 * public/ sebagai document root. Solusi yang dipakai:
 *
 * 1. Upload seluruh project ke htdocs/laravel-crud/
 * 2. Salin isi public/ (assets, css, favicon.ico, index.php, .htaccess)
 *    ke htdocs/ (document root)
 * 3. Ganti isi htdocs/index.php dengan:
 *
 *    <?php
 *    require __DIR__.'/laravel-crud/server.php';
 *
 * File ini mengarahkan bootstrap ke public/index.php milik Laravel.
 */

$publicPath = __DIR__.'/laravel-crud/public';

if (! is_dir($publicPath)) {
    $publicPath = __DIR__.'/public';
}

if (is_dir($publicPath)) {
    require $publicPath.'/index.php';
} else {
    http_response_code(500);
    die('Folder public tidak ditemukan. Periksa struktur project dan isi server.php.');
}
