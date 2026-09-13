<?php
// clearcache.php - HAPUS FILE INI SETELAH DIPAKAI!
// Upload ke ROOT htdocs/, buka https://farshalaravel.free.nf/clearcache.php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '<pre>';
try {
    Artisan::call('config:clear');
    echo 'config:clear => '.Artisan::output();

    Artisan::call('route:clear');
    echo 'route:clear => '.Artisan::output();

    Artisan::call('view:clear');
    echo 'view:clear => '.Artisan::output();

    Artisan::call('cache:clear');
    echo 'cache:clear => '.Artisan::output();

    echo "\nSELESAI. Sekarang HAPUS file clearcache.php di server.\n";
} catch (Throwable $e) {
    echo "GAGAL:\n".$e->getMessage()."\n";
}
echo '</pre>';