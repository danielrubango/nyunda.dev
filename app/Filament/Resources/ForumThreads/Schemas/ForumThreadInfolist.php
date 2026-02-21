<?php

namespace App\Filament\Resources\ForumThreads\Schemas;

use App\Models\ForumThread;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ForumThreadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thread')
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpanFull(),
                        TextEntry::make('slug'),
                        TextEntry::make('author.name')
                            ->label('Author'),
                        TextEntry::make('locale')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => Str::upper($state)),
                        IconEntry::make('is_visible')
                            ->label('Visible')
                            ->state(fn (ForumThread $record): bool => ! $record->is_hidden)
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger'),
                        IconEntry::make('has_best_reply')
                            ->label('Best reply')
                            ->state(fn (ForumThread $record): bool => $record->best_reply_id !== null)
                            ->boolean(),
                        TextEntry::make('replies_count')
                            ->label('Replies')
                            ->state(fn (ForumThread $record): int => $record->replies()->count()),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                        TextEntry::make('body_markdown')
                            ->label('Contenu')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
