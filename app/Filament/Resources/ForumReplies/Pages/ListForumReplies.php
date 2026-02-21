<?php

namespace App\Filament\Resources\ForumReplies\Pages;

use App\Filament\Resources\ForumReplies\ForumReplyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForumReplies extends ListRecords
{
    protected static string $resource = ForumReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
