<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Actions\Content\TransitionContentItemStatus;
use App\Enums\ContentStatus;
use App\Filament\Resources\ContentItems\ContentItemResource;
use App\Models\ContentItem;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;

class ViewContentItem extends ViewRecord
{
    protected static string $resource = ContentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
        ];
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
