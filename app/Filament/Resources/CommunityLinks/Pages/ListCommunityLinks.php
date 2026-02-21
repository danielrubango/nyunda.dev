<?php

namespace App\Filament\Resources\CommunityLinks\Pages;

use App\Filament\Resources\CommunityLinks\CommunityLinkResource;
use App\Filament\Resources\ContentItems\Pages\ListContentItems;

class ListCommunityLinks extends ListContentItems
{
    protected static string $resource = CommunityLinkResource::class;
}
