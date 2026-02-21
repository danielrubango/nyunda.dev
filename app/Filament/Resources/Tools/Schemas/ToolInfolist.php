<?php

namespace App\Filament\Resources\Tools\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ToolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('url')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab(),
                IconEntry::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                TextEntry::make('sort_order'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ])
            ->columns(3);
    }
}
