<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel akun (custom auth, bukan tabel users bawaan Laravel).
     */
    public function up(): void
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->integer('id_akun', true);
            $table->string('nama', 100);
            $table->string('username', 100);
            $table->string('email', 50);
            $table->string('password', 100);
            $table->string('level', 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
