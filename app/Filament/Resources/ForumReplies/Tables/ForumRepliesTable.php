<?php

namespace App\Filament\Resources\ForumReplies\Tables;

use App\Models\ForumReply;
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

class ForumRepliesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('forumThread.title')
                    ->label('Thread')
                    ->searchable()
                    ->limit(60)
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->state(fn (ForumReply $record): bool => ! $record->is_hidden)
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('is_best_reply')
                    ->label('Best')
                    ->state(fn (ForumReply $record): bool => $record->forumThread?->best_reply_id === $record->id)
                    ->boolean(),
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
                        'yes' => 'Best replies only',
                        'no' => 'Non-best replies',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->whereHas('forumThread', fn (Builder $builder): Builder => $builder->whereColumn('best_reply_id', 'forum_replies.id')),
                            'no' => $query->whereHas('forumThread', fn (Builder $builder): Builder => $builder->where(function (Builder $inner): Builder {
                                return $inner
                                    ->whereNull('best_reply_id')
                                    ->orWhereColumn('best_reply_id', '!=', 'forum_replies.id');
                            })),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->slideOver(),
                    Action::make('toggle_visibility')
                        ->label(fn (ForumReply $record): string => $record->is_hidden ? 'Show' : 'Hide')
                        ->requiresConfirmation()
                        ->action(function (ForumReply $record): void {
                            $isCurrentlyHidden = $record->is_hidden;

                            $record->update([
                                'is_hidden' => ! $isCurrentlyHidden,
                                'hidden_at' => $isCurrentlyHidden ? null : now(),
                                'hidden_by_id' => $isCurrentlyHidden ? null : auth()->id(),
                            ]);

                            if (! $isCurrentlyHidden) {
                                $record->forumThread?->update([
                                    'best_reply_id' => $record->forumThread?->best_reply_id === $record->id
                                        ? null
                                        : $record->forumThread?->best_reply_id,
                                ]);
                            }
                        }),
                    Action::make('mark_best_reply')
                        ->label('Mark best reply')
                        ->visible(fn (ForumReply $record): bool => $record->forumThread?->best_reply_id !== $record->id)
                        ->requiresConfirmation()
                        ->action(function (ForumReply $record): void {
                            $record->forumThread?->update([
                                'best_reply_id' => $record->id,
                            ]);
                        }),
                    Action::make('clear_best_reply')
                        ->label('Clear best reply')
                        ->visible(fn (ForumReply $record): bool => $record->forumThread?->best_reply_id === $record->id)
                        ->requiresConfirmation()
                        ->action(function (ForumReply $record): void {
                            $record->forumThread?->update([
                                'best_reply_id' => null,
                            ]);
                        }),
                    EditAction::make()
                        ->slideOver(),
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
