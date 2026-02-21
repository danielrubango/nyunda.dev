<?php

namespace App\Filament\Resources\ContentItems\Schemas;

use App\Enums\ContentType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ContentItemForm
{
    public static function configure(Schema $schema, ?ContentType $forcedType = null): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        $forcedType
                            ? Hidden::make('type')->default($forcedType->value)
                            : Select::make('type')
                                ->options(collect(ContentType::cases())->mapWithKeys(
                                    fn (ContentType $type): array => [
                                        $type->value => Str::headline($type->value),
                                    ],
                                )->all())
                                ->required()
                                ->live(),
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
                            ->label('Approved at')
                            ->native(false)
                            ->readOnly()
                            ->dehydrated(false),
                        DateTimePicker::make('published_at')
                            ->label('Published at')
                            ->native(false)
                            ->readOnly()
                            ->dehydrated(false),
                        Toggle::make('show_likes')
                            ->default(true)
                            ->visible(fn (Get $get): bool => self::isInternalType($get, $forcedType))
                            ->dehydrated(fn (Get $get): bool => self::isInternalType($get, $forcedType)),
                        Toggle::make('show_comments')
                            ->default(true)
                            ->visible(fn (Get $get): bool => self::isInternalType($get, $forcedType))
                            ->dehydrated(fn (Get $get): bool => self::isInternalType($get, $forcedType)),
                        Toggle::make('share_on_publish')
                            ->default(false),
                    ])
                    ->columns(3),
                Section::make('Default translation')
                    ->visibleOn('create')
                    ->schema([
                        Select::make('initial_locale')
                            ->label('Locale')
                            ->default(app()->getLocale())
                            ->options(collect(config('app.supported_locales', ['fr', 'en']))->mapWithKeys(
                                fn (string $locale): array => [
                                    $locale => Str::upper($locale),
                                ],
                            )->all())
                            ->required(),
                        TextInput::make('initial_title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $old): void {
                                $currentSlug = (string) $get('initial_slug');
                                $oldSlug = Str::slug((string) $old);

                                if ($currentSlug === '' || $currentSlug === $oldSlug) {
                                    $set('initial_slug', Str::slug((string) $state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('initial_slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('initial_excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->columnSpanFull(),
                        MarkdownEditor::make('initial_body_markdown')
                            ->label('Body')
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['heading', 'codeBlock'],
                                ['bulletList', 'orderedList'],
                                ['attachFiles', 'undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('content-markdown')
                            ->visible(fn (Get $get): bool => self::isInternalType($get, $forcedType))
                            ->required(fn (Get $get): bool => self::isInternalType($get, $forcedType))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                if ((string) $get('initial_excerpt') !== '') {
                                    return;
                                }

                                $plainText = trim((string) Str::of(strip_tags((string) Str::markdown((string) $state)))->squish());
                                $set('initial_excerpt', Str::limit($plainText, 200));
                            })
                            ->columnSpanFull(),
                        TextInput::make('initial_external_url')
                            ->label('External URL')
                            ->url()
                            ->maxLength(2048)
                            ->visible(fn (Get $get): bool => ! self::isInternalType($get, $forcedType))
                            ->required(fn (Get $get): bool => ! self::isInternalType($get, $forcedType))
                            ->columnSpanFull(),
                        TextInput::make('initial_external_site_name')
                            ->label('External site')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => ! self::isInternalType($get, $forcedType)),
                        TextInput::make('initial_external_og_image_url')
                            ->label('External OG image')
                            ->url()
                            ->maxLength(2048)
                            ->visible(fn (Get $get): bool => ! self::isInternalType($get, $forcedType)),
                        Textarea::make('initial_external_description')
                            ->label('External description')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => ! self::isInternalType($get, $forcedType))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                if ((string) $get('initial_excerpt') !== '') {
                                    return;
                                }

                                $plainText = trim((string) Str::of((string) $state)->squish());
                                $set('initial_excerpt', Str::limit($plainText, 200));
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    protected static function isInternalType(Get $get, ?ContentType $forcedType): bool
    {
        $type = $forcedType?->value ?? (string) $get('type');

        return $type === ContentType::InternalPost->value;
    }
}
