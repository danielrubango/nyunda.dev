<?php

namespace App\Filament\Resources\ContentItems\Support;

use App\Actions\Content\TransitionContentItemStatus;
use App\Enums\ContentStatus;
use App\Models\ContentItem;
use Filament\Actions\Action;

class ContentItemStatusActions
{
    public static function approve(): Action
    {
        return Action::make('approve')
            ->label('Approve')
            ->color('gray')
            ->visible(function (ContentItem $record): bool {
                return $record->status === ContentStatus::Pending
                    && auth()->id() !== $record->author_id;
            })
            ->requiresConfirmation()
            ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                $transitionContentItemStatus->handle($record, ContentStatus::Published);
            });
    }

    public static function publish(): Action
    {
        return Action::make('publish')
            ->label('Publish')
            ->color('success')
            ->visible(function (ContentItem $record): bool {
                if ($record->status === ContentStatus::Published) {
                    return false;
                }

                if (
                    $record->status === ContentStatus::Pending
                    && auth()->id() !== $record->author_id
                ) {
                    return false;
                }

                return true;
            })
            ->requiresConfirmation()
            ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                $transitionContentItemStatus->handle($record, ContentStatus::Published);
            });
    }

    public static function unpublish(): Action
    {
        return Action::make('unpublish')
            ->label('Unpublish')
            ->color('warning')
            ->visible(fn (ContentItem $record): bool => $record->status === ContentStatus::Published)
            ->requiresConfirmation()
            ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                $transitionContentItemStatus->handle($record, ContentStatus::Pending);
            });
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->color('gray')
            ->visible(function (ContentItem $record): bool {
                if (! in_array($record->status, [
                    ContentStatus::Pending,
                    ContentStatus::Published,
                ], true)) {
                    return false;
                }

                if (
                    $record->status === ContentStatus::Published
                    && auth()->id() === $record->author_id
                ) {
                    return false;
                }

                return true;
            })
            ->requiresConfirmation()
            ->action(function (ContentItem $record, TransitionContentItemStatus $transitionContentItemStatus): void {
                $transitionContentItemStatus->handle($record, ContentStatus::Rejected);
            });
    }
}
