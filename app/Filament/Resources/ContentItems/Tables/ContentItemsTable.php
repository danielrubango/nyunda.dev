<?php

namespace App\Filament\Resources\ContentItems\Tables;

use App\Actions\Content\TransitionContentItemStatus;
use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (ContentType|string $state): string => Str::headline(
                        $state instanceof ContentType ? $state->value : $state,
                    ))
                    ->sortable(),
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
                    ->label('Likes'),
                IconColumn::make('show_comments')
                    ->boolean()
                    ->label('Comments'),
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
                    )->all()),
            ])
            ->recordActions([
                Action::make('mark_pending')
                    ->label('Mark pending')
                    ->color('warning')
                    ->visible(fn (ContentItem $record): bool => in_array($record->status, [
                        ContentStatus::Draft,
                        ContentStatus::Rejected,
                    ], true))
                    ->requiresConfirmation()
                    ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                        $transitionContentItemStatus->handle($record, ContentStatus::Pending);
                    }),
                Action::make('publish')
                    ->label('Publish')
                    ->color('success')
                    ->visible(fn (ContentItem $record): bool => $record->status !== ContentStatus::Published)
                    ->requiresConfirmation()
                    ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                        $transitionContentItemStatus->handle($record, ContentStatus::Published);
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->visible(fn (ContentItem $record): bool => in_array($record->status, [
                        ContentStatus::Pending,
                        ContentStatus::Published,
                    ], true))
                    ->requiresConfirmation()
                    ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                        $transitionContentItemStatus->handle($record, ContentStatus::Rejected);
                    }),
                Action::make('move_to_draft')
                    ->label('Move to draft')
                    ->color('gray')
                    ->visible(fn (ContentItem $record): bool => $record->status !== ContentStatus::Draft)
                    ->requiresConfirmation()
                    ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                        $transitionContentItemStatus->handle($record, ContentStatus::Draft);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
