<?php

namespace App\Filament\Resources\ForumReplies\Schemas;

use App\Models\ForumReply;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ForumReplyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('forumThread.title')
                    ->label('Thread')
                    ->columnSpan(2),
                TextEntry::make('user.name')
                    ->label('User'),
                IconEntry::make('is_visible')
                    ->label('Visible')
                    ->state(fn (ForumReply $record): bool => ! $record->is_hidden)
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconEntry::make('is_best_reply')
                    ->label('Best')
                    ->state(fn (ForumReply $record): bool => $record->forumThread?->best_reply_id === $record->id)
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('body_markdown')
                    ->label('Contenu')
                    ->markdown()
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
