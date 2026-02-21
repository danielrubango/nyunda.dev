<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Models\Tag;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TagInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('sort_order'),
                TextEntry::make('content_items_count')
                    ->label('Content items')
                    ->state(fn (Tag $record): int => $record->contentItems()->count()),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ])
            ->columns(3);
    }
}
