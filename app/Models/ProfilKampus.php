<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilKampus extends Model
{
    use HasFactory;

    protected $table = 'profil_kampus';

    protected $fillable = [
        'nama_kampus',
        'alamat_kampus',
        'telepon',
        'email',
        'logo_url',
        'website',
        'petugas_pembayaran',
        'tahun_akademik',
        'is_tahun_akademik_aktif',
        'yudisium_start_date',
        'yudisium_end_date',
        'yudisium_is_open',
        'wa_api_url',
        'wa_api_key',
    ];

    protected function casts(): array
    {
        return [
            'is_tahun_akademik_aktif' => 'boolean',
            'yudisium_is_open' => 'boolean',
            'yudisium_start_date' => 'date',
            'yudisium_end_date' => 'date',
        ];
    }
}
