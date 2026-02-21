<?php

namespace App\Filament\Resources\ExternalPosts\Pages;

use App\Filament\Resources\ContentItems\Pages\ListContentItems;
use App\Filament\Resources\ExternalPosts\ExternalPostResource;

class ListExternalPosts extends ListContentItems
{
    protected static string $resource = ExternalPostResource::class;
}
