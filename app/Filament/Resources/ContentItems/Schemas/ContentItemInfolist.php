<?php

namespace App\Filament\Resources\ContentItems\Schemas;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ContentItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenu')
                    ->schema([
                        TextEntry::make('translations.title')
                            ->label('Title')
                            ->listWithLineBreaks()
                            ->limitList(1)
                            ->columnSpan(2),
                        TextEntry::make('type')
                            ->badge()
                            ->formatStateUsing(fn (ContentType|string $state): string => Str::headline(
                                $state instanceof ContentType ? $state->value : $state,
                            )),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ContentStatus|string $state): string => Str::headline(
                                $state instanceof ContentStatus ? $state->value : $state,
                            )),
                        TextEntry::make('author.name')
                            ->label('Author'),
                        TextEntry::make('tags.name')
                            ->label('Tags')
                            ->badge(),
                        TextEntry::make('translations_count')
                            ->label('Translations')
                            ->state(fn (ContentItem $record): int => $record->translations()->count()),
                    ])
                    ->columns(3),
                Section::make('Publication')
                    ->schema([
                        IconEntry::make('show_likes')
                            ->label('Likes visibles')
                            ->boolean(),
                        IconEntry::make('show_comments')
                            ->label('Commentaires visibles')
                            ->boolean(),
                        IconEntry::make('share_on_publish')
                            ->label('Partager à la publication')
                            ->boolean(),
                        TextEntry::make('reads_count')
                            ->label('Reads')
                            ->numeric(),
                        TextEntry::make('approved_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('published_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }
}
