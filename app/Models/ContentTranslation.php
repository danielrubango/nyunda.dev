<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\ContentTranslationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'content_item_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'body_markdown',
        'external_url',
        'external_description',
        'external_site_name',
        'external_og_image_url',
    ];

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }
}
