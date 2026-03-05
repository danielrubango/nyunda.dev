<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Actions\Content\CreateInitialTranslationForContentItem;
use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Filament\Resources\ContentItems\ContentItemResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Arr;

class CreateContentItem extends CreateRecord
{
    protected static string $resource = ContentItemResource::class;

    /**
     * @var array<string, mixed>
     */
    protected array $initialTranslationData = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialTranslationData = Arr::only($data, [
            'initial_locale',
            'initial_title',
            'initial_slug',
            'initial_excerpt',
            'initial_body_markdown',
            'initial_external_url',
            'initial_external_description',
            'initial_external_site_name',
            'initial_external_og_image_url',
        ]);

        $data = Arr::except($data, array_keys($this->initialTranslationData));

        if ($forcedType = $this->getForcedType()) {
            $data['type'] = $forcedType->value;
        }

        $data['status'] = $data['status'] ?? ContentStatus::Draft->value;

        if (($data['type'] ?? null) !== ContentType::InternalPost->value) {
            $data['show_likes'] = false;
            $data['show_comments'] = false;
            $data['prev_article_id'] = null;
            $data['next_article_id'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        app(CreateInitialTranslationForContentItem::class)->handle(
            $this->record,
            $this->initialTranslationData,
        );
    }

    protected function getForcedType(): ?ContentType
    {
        return null;
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
