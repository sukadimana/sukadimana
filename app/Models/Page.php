<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'title',
        'slug',
        'content',
        'is_published',
        'menu_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
