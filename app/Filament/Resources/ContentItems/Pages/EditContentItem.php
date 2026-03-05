<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Filament\Resources\ContentItems\ContentItemResource;
use App\Filament\Resources\ContentItems\Support\ContentItemStatusActions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditContentItem extends EditRecord
{
    protected static string $resource = ContentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentItemStatusActions::approve(),
            ContentItemStatusActions::publish(),
            ContentItemStatusActions::unpublish(),
            ContentItemStatusActions::reject(),
            DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $contentType = $this->record->type;

        if ($contentType !== null && ! $this->record->isInternalPost()) {
            $data['show_likes'] = false;
            $data['show_comments'] = false;
            $data['prev_article_id'] = null;
            $data['next_article_id'] = null;
        }

        return $data;
    }
}
