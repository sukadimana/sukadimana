<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'master_biaya_id',
        'nominal_awal',
        'potongan_beasiswa',
        'total_harus_bayar',
        'total_sudah_bayar',
        'sisa_tagihan',
        'status',
        'nama_tagihan_snapshot',
        'nama_mahasiswa_snapshot',
        'semester',
        'kategori_snapshot',
        'jatuh_tempo',
        'tahun_akademik_id_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'nominal_awal' => 'decimal:2',
            'potongan_beasiswa' => 'decimal:2',
            'total_harus_bayar' => 'decimal:2',
            'total_sudah_bayar' => 'decimal:2',
            'sisa_tagihan' => 'decimal:2',
            'jatuh_tempo' => 'date',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function masterBiaya(): BelongsTo
    {
        return $this->belongsTo(MasterBiaya::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function dispensasis(): HasMany
    {
        return $this->hasMany(Dispensasi::class);
    }
}
