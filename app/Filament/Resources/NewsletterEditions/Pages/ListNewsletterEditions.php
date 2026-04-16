<?php

namespace App\Filament\Resources\NewsletterEditions\Pages;

use App\Filament\Resources\NewsletterEditions\NewsletterEditionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterEditions extends ListRecords
{
    protected static string $resource = NewsletterEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
