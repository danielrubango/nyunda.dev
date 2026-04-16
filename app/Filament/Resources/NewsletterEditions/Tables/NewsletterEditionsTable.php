<?php

namespace App\Filament\Resources\NewsletterEditions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsletterEditionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject_fr')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->label('Statut'),
                TextColumn::make('recipients_count')
                    ->label('Destinataires')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sent_count')
                    ->label('Envoyés')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Envoi démarré')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
