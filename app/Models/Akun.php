<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Akun extends Authenticatable
{
    public $timestamps = false;

    protected $table = 'akun';

    protected $primaryKey = 'id_akun';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'level',
    ];

    protected $hidden = [
        'password',
    ];
}
