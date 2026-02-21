<?php

namespace App\Filament\Resources\ContentItems\Tables;

use App\Actions\Content\TransitionContentItemStatus;
use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\ContentItem;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ToggleButtons;
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
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('change_status')
                        ->label('Change status')
                        ->color('warning')
                        ->fillForm(function (ContentItem $record): array {
                            $currentStatus = $record->status instanceof ContentStatus
                                ? $record->status->value
                                : (string) $record->status;

                            return [
                                'status' => $currentStatus,
                            ];
                        })
                        ->form([
                            ToggleButtons::make('status')
                                ->label('Status')
                                ->inline()
                                ->options(collect(ContentStatus::cases())->mapWithKeys(
                                    fn (ContentStatus $status): array => [
                                        $status->value => Str::headline($status->value),
                                    ],
                                )->all())
                                ->required(),
                        ])
                        ->action(function (ContentItem $record, array $data, TransitionContentItemStatus $transitionContentItemStatus): void {
                            $transitionContentItemStatus->handle(
                                $record,
                                ContentStatus::from((string) $data['status']),
                            );
                        }),
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
