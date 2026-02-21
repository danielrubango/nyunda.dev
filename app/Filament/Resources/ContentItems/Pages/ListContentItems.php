<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Enums\ContentStatus;
use App\Filament\Resources\ContentItems\ContentItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListContentItems extends ListRecords
{
    protected static string $resource = ContentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge((string) static::getResource()::getEloquentQuery()->count()),
            ContentStatus::Draft->value => Tab::make(Str::headline(ContentStatus::Draft->value))
                ->badge((string) $this->getStatusCount(ContentStatus::Draft))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Draft->value)),
            ContentStatus::Pending->value => Tab::make(Str::headline(ContentStatus::Pending->value))
                ->badge((string) $this->getStatusCount(ContentStatus::Pending))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Pending->value)),
            ContentStatus::Published->value => Tab::make(Str::headline(ContentStatus::Published->value))
                ->badge((string) $this->getStatusCount(ContentStatus::Published))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Published->value)),
            ContentStatus::Rejected->value => Tab::make(Str::headline(ContentStatus::Rejected->value))
                ->badge((string) $this->getStatusCount(ContentStatus::Rejected))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Rejected->value)),
        ];
    }

    protected function getStatusCount(ContentStatus $status): int
    {
        return static::getResource()::getEloquentQuery()
            ->where('status', $status->value)
            ->count();
    }
}
