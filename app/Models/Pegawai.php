<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    public $timestamps = false;

    protected $table = 'pegawai';

    protected $primaryKey = 'id_pegawai';

    protected $fillable = [
        'nama',
        'jabatan',
        'email',
        'telepon',
        'alamat',
        'tanggal_masuk',
    ];
}
