<?php

namespace App\Livewire;

use App\Enums\ContentStatus;
use App\Filament\Resources\ContentItems\ContentItemResource;
use App\Models\ContentItem;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PendingQueueTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ContentItem::query()
                ->where('status', ContentStatus::Pending->value)
                ->with('author')
                ->latest('created_at'))
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([
                Action::make('review')
                    ->label('Review')
                    ->url(fn (ContentItem $record): string => ContentItemResource::getUrl('edit', [
                        'record' => $record,
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.pending-queue-table');
    }
}
