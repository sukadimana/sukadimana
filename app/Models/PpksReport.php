<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PpksReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'reporter_name',
        'reporter_contact',
        'is_anonymous',
        'description',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
        ];
    }

    public static function generateReferenceCode(): string
    {
        do {
            $code = 'PPKS-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (self::where('reference_code', $code)->exists());

        return $code;
    }
}
