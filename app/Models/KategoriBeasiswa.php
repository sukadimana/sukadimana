<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBeasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tipe_potongan',
    ];
}
