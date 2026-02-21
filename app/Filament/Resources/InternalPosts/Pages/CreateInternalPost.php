<?php

namespace App\Filament\Resources\InternalPosts\Pages;

use App\Enums\ContentType;
use App\Filament\Resources\ContentItems\Pages\CreateContentItem;
use App\Filament\Resources\InternalPosts\InternalPostResource;

class CreateInternalPost extends CreateContentItem
{
    protected static string $resource = InternalPostResource::class;

    protected function getForcedType(): ?ContentType
    {
        return ContentType::InternalPost;
    }
}
