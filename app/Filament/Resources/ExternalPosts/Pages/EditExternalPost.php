<?php

namespace App\Filament\Resources\ExternalPosts\Pages;

use App\Filament\Resources\ContentItems\Pages\EditContentItem;
use App\Filament\Resources\ExternalPosts\ExternalPostResource;

class EditExternalPost extends EditContentItem
{
    protected static string $resource = ExternalPostResource::class;
}
