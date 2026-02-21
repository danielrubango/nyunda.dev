<?php

namespace App\Filament\Resources\ContentItems\Tables;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Filament\Resources\ContentItems\Support\ContentItemStatusActions;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentItemsTable
{
    public static function configure(Table $table, ?ContentType $forcedType = null): Table
    {
        $showTypeColumn = $forcedType === null;
        $showInteractionColumns = $forcedType === null || $forcedType === ContentType::InternalPost;

        return $table
            ->columns([
                TextColumn::make('translations.title')
                    ->label('Title')
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->searchable()
                    ->sortable(false),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (ContentType|string $state): string => Str::headline(
                        $state instanceof ContentType ? $state->value : $state,
                    ))
                    ->sortable()
                    ->visible($showTypeColumn),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ContentStatus|string $state): string => Str::headline(
                        $state instanceof ContentStatus ? $state->value : $state,
                    ))
                    ->sortable(),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('translations_count')
                    ->counts('translations')
                    ->label('Translations')
                    ->sortable(),
                IconColumn::make('show_likes')
                    ->boolean()
                    ->label('Likes')
                    ->visible($showInteractionColumns),
                IconColumn::make('show_comments')
                    ->boolean()
                    ->label('Comments')
                    ->visible($showInteractionColumns),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(ContentStatus::cases())->mapWithKeys(
                        fn (ContentStatus $status): array => [
                            $status->value => Str::headline($status->value),
                        ],
                    )->all()),
                SelectFilter::make('type')
                    ->options(collect(ContentType::cases())->mapWithKeys(
                        fn (ContentType $type): array => [
                            $type->value => Str::headline($type->value),
                        ],
                    )->all())
                    ->visible($showTypeColumn),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    ContentItemStatusActions::approve(),
                    ContentItemStatusActions::publish(),
                    ContentItemStatusActions::unpublish(),
                    ContentItemStatusActions::reject(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->label('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
