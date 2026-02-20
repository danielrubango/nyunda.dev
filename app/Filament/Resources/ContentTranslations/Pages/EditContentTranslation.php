<?php

namespace App\Filament\Resources\ContentTranslations\Pages;

use App\Filament\Resources\ContentTranslations\ContentTranslationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentTranslation extends EditRecord
{
    protected static string $resource = ContentTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
