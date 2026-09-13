<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->integer('id_pegawai', true);
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->string('email', 100);
            $table->string('telepon', 100);
            $table->text('alamat');
            $table->date('tanggal_masuk')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
