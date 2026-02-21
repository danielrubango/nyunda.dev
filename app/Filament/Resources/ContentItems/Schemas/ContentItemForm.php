<?php

namespace App\Filament\Resources\ContentItems\Schemas;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ContentItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        Select::make('type')
                            ->options(collect(ContentType::cases())->mapWithKeys(
                                fn (ContentType $type): array => [
                                    $type->value => Str::headline($type->value),
                                ],
                            )->all())
                            ->required(),
                        Select::make('status')
                            ->options(collect(ContentStatus::cases())->mapWithKeys(
                                fn (ContentStatus $status): array => [
                                    $status->value => Str::headline($status->value),
                                ],
                            )->all())
                            ->default(ContentStatus::Draft->value)
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Use row actions to transition the status.'),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('approved_at')
                            ->native(false),
                        DateTimePicker::make('published_at')
                            ->native(false),
                        Toggle::make('show_likes')
                            ->default(true),
                        Toggle::make('show_comments')
                            ->default(true),
                        Toggle::make('share_on_publish')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
