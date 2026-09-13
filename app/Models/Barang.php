<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    public $timestamps = false;

    protected $table = 'barang';

    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'nama',
        'jumlah',
        'harga',
        'barcode',
        'tanggal',
    ];
}
