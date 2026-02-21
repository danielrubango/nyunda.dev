<?php

namespace App\Filament\Resources\CommunityLinks\Pages;

use App\Enums\ContentType;
use App\Filament\Resources\CommunityLinks\CommunityLinkResource;
use App\Filament\Resources\ContentItems\Pages\CreateContentItem;

class CreateCommunityLink extends CreateContentItem
{
    protected static string $resource = CommunityLinkResource::class;

    protected function getForcedType(): ?ContentType
    {
        return ContentType::CommunityLink;
    }
}
