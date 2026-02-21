<?php

namespace App\Filament\Resources\ForumReplies\Pages;

use App\Filament\Resources\ForumReplies\ForumReplyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateForumReply extends CreateRecord
{
    protected static string $resource = ForumReplyResource::class;
}
