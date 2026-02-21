<?php

namespace App\Filament\Resources\Subscribers\Tables;

use App\Enums\SubscriberStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (SubscriberStatus|string $state): string => Str::headline(
                        $state instanceof SubscriberStatus ? $state->value : $state,
                    ))
                    ->sortable(),
                TextColumn::make('locale')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::upper($state)),
                TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(SubscriberStatus::cases())->mapWithKeys(
                        fn (SubscriberStatus $status): array => [
                            $status->value => Str::headline($status->value),
                        ],
                    )->all()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->slideOver(),
                    EditAction::make()
                        ->slideOver(),
                    DeleteAction::make(),
                ])->label('Actions'),
            ])
            ->headerActions([
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->url(fn (): string => route('admin.subscribers.export'))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
