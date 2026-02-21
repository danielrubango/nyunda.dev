<?php

namespace App\Filament\Resources\InternalPosts\Pages;

use App\Filament\Resources\ContentItems\Pages\EditContentItem;
use App\Filament\Resources\InternalPosts\InternalPostResource;

class EditInternalPost extends EditContentItem
{
    protected static string $resource = InternalPostResource::class;
}
