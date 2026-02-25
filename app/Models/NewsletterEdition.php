<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterEdition extends Model
{
    /** @use HasFactory<\Database\Factories\NewsletterEditionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'subject_fr',
        'subject_en',
        'intro_fr',
        'intro_en',
        'content_item_ids',
        'status',
        'recipients_count',
        'sent_count',
        'started_at',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_item_ids' => 'array',
            'recipients_count' => 'integer',
            'sent_count' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ContentItem>
     */
    public function contentItems(): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($this->content_item_ids)) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return ContentItem::query()
            ->with('translations')
            ->whereIn('id', $this->content_item_ids)
            ->get();
    }
}
