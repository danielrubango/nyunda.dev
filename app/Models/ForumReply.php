<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumReply extends Model
{
    /** @use HasFactory<\Database\Factories\ForumReplyFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'forum_thread_id',
        'user_id',
        'body_markdown',
        'is_hidden',
        'hidden_at',
        'hidden_by_id',
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

    public function forumThread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hiddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by_id');
    }
}
