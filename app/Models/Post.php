<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'author_id',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
