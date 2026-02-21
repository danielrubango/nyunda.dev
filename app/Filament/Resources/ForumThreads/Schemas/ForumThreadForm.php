<?php

namespace App\Filament\Resources\ForumThreads\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ForumThreadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thread')
                    ->schema([
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('locale')
                            ->options(collect(config('app.supported_locales', ['fr', 'en']))->mapWithKeys(
                                fn (string $locale): array => [
                                    $locale => Str::upper($locale),
                                ],
                            )->all())
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(160)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old): void {
                                $currentSlug = (string) $get('slug');
                                $oldSlug = Str::slug((string) $old);

                                if ($currentSlug === '' || $currentSlug === $oldSlug) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(180)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        MarkdownEditor::make('body_markdown')
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['heading', 'codeBlock'],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull()
                            ->required(),
                        Toggle::make('is_hidden')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
