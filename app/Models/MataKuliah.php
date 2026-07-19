<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'sks',
        'prodi_id',
        'semester',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }
}
