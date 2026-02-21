<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Filament\Resources\ContentItems\ContentItemResource;
use App\Filament\Resources\ContentItems\Support\ContentItemStatusActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewContentItem extends ViewRecord
{
    protected static string $resource = ContentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentItemStatusActions::approve(),
            ContentItemStatusActions::publish(),
            ContentItemStatusActions::unpublish(),
            ContentItemStatusActions::reject(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
