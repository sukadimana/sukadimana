<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prodi_id',
        'nim',
        'nama_lengkap',
        'angkatan',
        'tahun_masuk',
        'status_akademik',
        'kategori_beasiswa',
        'beasiswa_potongan',
        'jenjang',
        'semester_saat_ini',
        'jenis_kelamin',
        'tahun_akademik_id_saat_ini',
    ];

    protected function casts(): array
    {
        return [
            'beasiswa_potongan' => 'decimal:2',
        ];
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function tahunAkademikSaatIni(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id_saat_ini');
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function yudisium(): HasMany
    {
        return $this->hasMany(Yudisium::class);
    }

    public function alumniTrackings(): HasMany
    {
        return $this->hasMany(AlumniTracking::class);
    }
}
