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
            'all' => Tab::make('Tous'),
            ContentStatus::Draft->value => Tab::make(Str::headline(ContentStatus::Draft->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Draft->value)),
            ContentStatus::Pending->value => Tab::make(Str::headline(ContentStatus::Pending->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Pending->value)),
            ContentStatus::Published->value => Tab::make(Str::headline(ContentStatus::Published->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Published->value)),
            ContentStatus::Rejected->value => Tab::make(Str::headline(ContentStatus::Rejected->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ContentStatus::Rejected->value)),
        ];
    }
}
