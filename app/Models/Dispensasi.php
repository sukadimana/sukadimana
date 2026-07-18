<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispensasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tagihan_id',
        'jenis_dispen',
        'nominal_potongan',
        'sk_dispen',
    ];

    protected function casts(): array
    {
        return [
            'nominal_potongan' => 'decimal:2',
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }
}
