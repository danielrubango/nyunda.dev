<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use App\Enums\SubscriberStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SubscriberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('email'),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (SubscriberStatus|string $state): string => Str::headline(
                        $state instanceof SubscriberStatus ? $state->value : $state,
                    )),
                TextEntry::make('locale')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::upper($state)),
                TextEntry::make('confirmed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ])
            ->columns(3);
    }
}
