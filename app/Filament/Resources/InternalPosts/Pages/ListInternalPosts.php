<?php

namespace App\Filament\Resources\InternalPosts\Pages;

use App\Filament\Resources\ContentItems\Pages\ListContentItems;
use App\Filament\Resources\InternalPosts\InternalPostResource;

class ListInternalPosts extends ListContentItems
{
    protected static string $resource = InternalPostResource::class;
}
