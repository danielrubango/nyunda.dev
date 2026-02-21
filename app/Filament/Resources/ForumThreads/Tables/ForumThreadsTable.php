<?php

namespace App\Filament\Resources\ForumThreads\Tables;

use App\Models\ForumThread;
use Filament\Actions\Action;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ForumThreadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(80)
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locale')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::upper($state))
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->state(fn (ForumThread $record): bool => ! $record->is_hidden)
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('has_best_reply')
                    ->label('Best reply')
                    ->state(fn (ForumThread $record): bool => $record->best_reply_id !== null)
                    ->boolean(),
                TextColumn::make('replies_count')
                    ->counts('replies')
                    ->label('Replies')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('visibility')
                    ->label('Visibility')
                    ->options([
                        'visible' => 'Visible',
                        'hidden' => 'Hidden',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'visible' => $query->where('is_hidden', false),
                            'hidden' => $query->where('is_hidden', true),
                            default => $query,
                        };
                    }),
                SelectFilter::make('best_reply')
                    ->label('Best reply')
                    ->options([
                        'yes' => 'Has best reply',
                        'no' => 'No best reply',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->whereNotNull('best_reply_id'),
                            'no' => $query->whereNull('best_reply_id'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('toggle_visibility')
                        ->label(fn (ForumThread $record): string => $record->is_hidden ? 'Show' : 'Hide')
                        ->requiresConfirmation()
                        ->action(function (ForumThread $record): void {
                            $isCurrentlyHidden = $record->is_hidden;

                            $record->update([
                                'is_hidden' => ! $isCurrentlyHidden,
                                'hidden_at' => $isCurrentlyHidden ? null : now(),
                                'hidden_by_id' => $isCurrentlyHidden ? null : auth()->id(),
                            ]);
                        }),
                    Action::make('clear_best_reply')
                        ->label('Clear best reply')
                        ->visible(fn (ForumThread $record): bool => $record->best_reply_id !== null)
                        ->requiresConfirmation()
                        ->action(function (ForumThread $record): void {
                            $record->update([
                                'best_reply_id' => null,
                            ]);
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
            ->defaultSort('created_at', 'desc');
    }
}
