<?php

namespace App\Filament\Resources\Tools\Pages;

use App\Filament\Resources\Tools\ToolResource;
use App\Models\Tool;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTools extends ListRecords
{
    protected static string $resource = ToolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(fn (array $data): array => [
                    ...$data,
                    'sort_order' => (Tool::query()->max('sort_order') ?? 0) + 1,
                ]),
        ];
    }
}
