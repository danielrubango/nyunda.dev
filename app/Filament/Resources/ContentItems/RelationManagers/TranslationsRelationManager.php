<?php

namespace App\Filament\Resources\ContentItems\RelationManagers;

use App\Models\ContentTranslation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class TranslationsRelationManager extends RelationManager
{
    protected static string $relationship = 'translations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('locale')
                    ->options(collect(config('app.supported_locales', ['fr', 'en']))->mapWithKeys(
                        fn (string $locale): array => [
                            $locale => Str::upper($locale),
                        ],
                    )->all())
                    ->required()
                    ->unique(
                        table: ContentTranslation::class,
                        column: 'locale',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where('content_item_id', $this->getOwnerRecord()->getKey()),
                    ),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old): void {
                        $currentSlug = (string) $get('slug');
                        $oldSlug = Str::slug((string) $old);

                        if ($currentSlug === '' || $currentSlug === $oldSlug) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Textarea::make('excerpt')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                MarkdownEditor::make('body_markdown')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['heading', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['attachFiles', 'undo', 'redo'],
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('content-markdown')
                    ->columnSpanFull(),
                TextInput::make('external_url')
                    ->url()
                    ->maxLength(2048)
                    ->columnSpanFull(),
                TextInput::make('external_site_name')
                    ->maxLength(255),
                Textarea::make('external_description')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('external_og_image_url')
                    ->url()
                    ->maxLength(2048)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('locale')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::upper($state))
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(70),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver(),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
