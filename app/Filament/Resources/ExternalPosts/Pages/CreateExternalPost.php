<?php

namespace App\Filament\Resources\ExternalPosts\Pages;

use App\Enums\ContentType;
use App\Filament\Resources\ContentItems\Pages\CreateContentItem;
use App\Filament\Resources\ExternalPosts\ExternalPostResource;

class CreateExternalPost extends CreateContentItem
{
    protected static string $resource = ExternalPostResource::class;

    protected function getForcedType(): ?ContentType
    {
        return ContentType::ExternalPost;
    }
}
