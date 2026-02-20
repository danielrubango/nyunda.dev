<?php

namespace App\Filament\Resources\ContentTranslations\Pages;

use App\Filament\Resources\ContentTranslations\ContentTranslationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentTranslations extends ListRecords
{
    protected static string $resource = ContentTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
