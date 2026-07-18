<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Yudisium extends Model
{
    use HasFactory;

    protected $table = 'yudisiums';

    protected $fillable = [
        'mahasiswa_id',
        'bebas_pustaka',
        'catatan_pustaka',
        'bebas_keuangan',
        'catatan_keuangan',
        'bebas_akademik',
        'catatan_akademik',
        'status_akhir',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
