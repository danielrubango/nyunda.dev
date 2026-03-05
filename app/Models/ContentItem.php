<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentItem extends Model
{
    /** @use HasFactory<\Database\Factories\ContentItemFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'status',
        'author_id',
        'approved_at',
        'published_at',
        'show_likes',
        'show_comments',
        'share_on_publish',
        'reads_count',
        'is_featured',
        'prev_article_id',
        'next_article_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ContentType::class,
            'status' => ContentStatus::class,
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
            'show_likes' => 'boolean',
            'show_comments' => 'boolean',
            'share_on_publish' => 'boolean',
            'reads_count' => 'integer',
            'is_featured' => 'boolean',
            'prev_article_id' => 'integer',
            'next_article_id' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ContentTranslation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'content_item_tag')
            ->withTimestamps();
    }

    public function prevArticle(): BelongsTo
    {
        return $this->belongsTo(self::class, 'prev_article_id');
    }

    public function nextArticle(): BelongsTo
    {
        return $this->belongsTo(self::class, 'next_article_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ContentLike::class);
    }

    public function contentReads(): HasMany
    {
        return $this->hasMany(ContentRead::class);
    }

    public function socialShareLogs(): HasMany
    {
        return $this->hasMany(SocialShareLog::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', ContentStatus::Published->value)
            ->where(function (Builder $nestedQuery): void {
                $nestedQuery
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function isPublished(): bool
    {
        if ($this->status !== ContentStatus::Published) {
            return false;
        }

        if ($this->published_at === null) {
            return true;
        }

        return $this->published_at->lte(now());
    }

    public function isInternalPost(): bool
    {
        return $this->type === ContentType::InternalPost;
    }

    public function isExternalPost(): bool
    {
        return $this->type === ContentType::ExternalPost;
    }
}
