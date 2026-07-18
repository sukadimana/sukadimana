<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    public const BOBOT = [
        'A' => 4.00,
        'B' => 3.00,
        'C' => 2.00,
        'D' => 1.00,
        'E' => 0.00,
    ];

    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'tahun_akademik_id',
        'nilai_huruf',
        'bobot',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }
}
