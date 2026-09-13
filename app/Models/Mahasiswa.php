<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    public $timestamps = false;

    protected $table = 'mahasiswa';

    protected $primaryKey = 'id_mahasiswa';

    protected $fillable = [
        'nama',
        'prodi',
        'jk',
        'telepon',
        'alamat',
        'email',
        'foto',
    ];
}
