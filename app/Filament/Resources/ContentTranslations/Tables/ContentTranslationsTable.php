<?php

namespace App\Filament\Resources\ContentTranslations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentTranslationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contentItem.type')
                    ->label('Content type')
                    ->sortable(),
                TextColumn::make('locale')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::upper($state))
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(70),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('locale')
                    ->options(collect(config('app.supported_locales', ['fr', 'en']))->mapWithKeys(
                        fn (string $locale): array => [
                            $locale => Str::upper($locale),
                        ],
                    )->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
