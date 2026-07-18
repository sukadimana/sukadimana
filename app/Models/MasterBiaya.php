<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterBiaya extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_biaya',
        'angkatan',
        'prodi_id',
        'nominal_baku',
        'bisa_dicicil',
        'semester',
        'jenjang',
    ];

    protected function casts(): array
    {
        return [
            'nominal_baku' => 'decimal:2',
            'bisa_dicicil' => 'boolean',
        ];
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}
