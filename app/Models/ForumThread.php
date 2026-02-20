<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumThread extends Model
{
    /** @use HasFactory<\Database\Factories\ForumThreadFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'locale',
        'title',
        'slug',
        'body_markdown',
        'is_hidden',
        'hidden_at',
        'hidden_by_id',
        'best_reply_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
            'hidden_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
    }

    public function bestReply(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'best_reply_id');
    }

    public function hiddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by_id');
    }
}
