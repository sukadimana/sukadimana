<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'tahun_lulus',
        'nomor_hp_terbaru',
        'alamat_domisili',
        'status_pekerjaan',
        'kategori_pekerjaan',
        'nama_instansi',
        'jurusan_lanjutan',
        'prodi_lanjutan',
        'jabatan',
        'gaji_pertama',
        'tanggal_isi',
    ];

    protected function casts(): array
    {
        return [
            'gaji_pertama' => 'decimal:2',
            'tanggal_isi' => 'date',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
