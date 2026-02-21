<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentRead extends Model
{
    /** @use HasFactory<\Database\Factories\ContentReadFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'content_item_id',
        'user_id',
        'visitor_fingerprint',
        'counted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'counted_at' => 'datetime',
        ];
    }

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
