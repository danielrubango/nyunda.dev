<?php

namespace App\Filament\Resources\ForumThreads\Pages;

use App\Filament\Resources\ForumThreads\ForumThreadResource;
use App\Models\ForumThread;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewForumThread extends ViewRecord
{
    protected static string $resource = ForumThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
        ];
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
