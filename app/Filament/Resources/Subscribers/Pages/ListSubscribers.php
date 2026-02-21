<?php

namespace App\Filament\Resources\Subscribers\Pages;

use App\Enums\SubscriberStatus;
use App\Filament\Resources\Subscribers\SubscriberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

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
            SubscriberStatus::Pending->value => Tab::make(Str::headline(SubscriberStatus::Pending->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', SubscriberStatus::Pending->value)),
            SubscriberStatus::Confirmed->value => Tab::make(Str::headline(SubscriberStatus::Confirmed->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', SubscriberStatus::Confirmed->value)),
            SubscriberStatus::Unsubscribed->value => Tab::make(Str::headline(SubscriberStatus::Unsubscribed->value))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', SubscriberStatus::Unsubscribed->value)),
        ];
    }
}
