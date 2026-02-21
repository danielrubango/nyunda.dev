<?php

namespace App\Filament\Resources\ForumReplies\Pages;

use App\Filament\Resources\ForumReplies\ForumReplyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForumReply extends EditRecord
{
    protected static string $resource = ForumReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
