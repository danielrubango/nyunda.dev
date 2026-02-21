<?php

namespace App\Filament\Resources\ContentItems\RelationManagers;

use App\Enums\ContentType;
use App\Models\ContentTranslation;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
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
                    ->options(fn (?ContentTranslation $record): array => $this->getAvailableLocaleOptions($record))
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
                    ->visible(fn (): bool => $this->isInternalType())
                    ->required(fn (): bool => $this->isInternalType())
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        if ((string) $get('excerpt') !== '') {
                            return;
                        }

                        $plainText = trim((string) Str::of(strip_tags((string) Str::markdown((string) $state)))->squish());
                        $set('excerpt', Str::limit($plainText, 200));
                    })
                    ->columnSpanFull(),
                TextInput::make('external_url')
                    ->url()
                    ->maxLength(2048)
                    ->visible(fn (): bool => ! $this->isInternalType())
                    ->required(fn (): bool => ! $this->isInternalType())
                    ->columnSpanFull(),
                TextInput::make('external_site_name')
                    ->visible(fn (): bool => ! $this->isInternalType())
                    ->maxLength(255),
                Textarea::make('external_description')
                    ->rows(3)
                    ->visible(fn (): bool => ! $this->isInternalType())
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        if ((string) $get('excerpt') !== '') {
                            return;
                        }

                        $plainText = trim((string) Str::of((string) $state)->squish());
                        $set('excerpt', Str::limit($plainText, 200));
                    })
                    ->columnSpanFull(),
                TextInput::make('external_og_image_url')
                    ->url()
                    ->maxLength(2048)
                    ->visible(fn (): bool => ! $this->isInternalType())
                    ->columnSpanFull(),
                TextInput::make('featured_image_url')
                    ->url()
                    ->maxLength(2048)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Translation')
                    ->schema([
                        TextEntry::make('locale')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => Str::upper($state)),
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('excerpt')
                            ->columnSpanFull(),
                        TextEntry::make('body_markdown')
                            ->label('Contenu')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Section::make('External')
                    ->schema([
                        TextEntry::make('external_url')
                            ->url(fn (?string $state): ?string => $state)
                            ->openUrlInNewTab(),
                        TextEntry::make('external_site_name'),
                        ImageEntry::make('external_og_image_url')
                            ->label('OG image')
                            ->defaultImageUrl(null)
                            ->columnSpanFull(),
                        ImageEntry::make('featured_image_url')
                            ->label('Featured image')
                            ->defaultImageUrl(null)
                            ->columnSpanFull(),
                        TextEntry::make('external_description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
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
                    ->visible(fn (): bool => count($this->getAvailableLocaleOptions()) > 0),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->label('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    /**
     * @return array<string, string>
     */
    protected function getAvailableLocaleOptions(?ContentTranslation $record = null): array
    {
        $supportedLocales = collect(config('app.supported_locales', ['fr', 'en']));

        /** @var Collection<int, string> $usedLocales */
        $usedLocales = $this->getOwnerRecord()
            ->translations()
            ->pluck('locale');

        if ($record !== null) {
            $usedLocales = $usedLocales->reject(fn (string $locale): bool => $locale === $record->locale);
        }

        return $supportedLocales
            ->reject(fn (string $locale): bool => $usedLocales->contains($locale))
            ->mapWithKeys(fn (string $locale): array => [$locale => Str::upper($locale)])
            ->all();
    }

    protected function isInternalType(): bool
    {
        $ownerType = $this->getOwnerRecord()->type;

        if ($ownerType instanceof ContentType) {
            return $ownerType === ContentType::InternalPost;
        }

        return (string) $ownerType === ContentType::InternalPost->value;
    }
}
