<?php

namespace App\Filament\Resources\ContentTranslations\Schemas;

use App\Enums\ContentType;
use App\Models\ContentItem;
use App\Support\SeoDescription;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ContentTranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Translation')
                    ->schema([
                        Select::make('content_item_id')
                            ->relationship('contentItem', 'id')
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
                            ->maxLength(255)
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
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->required()
                            ->rules(fn (Get $get): array => ContentItem::query()
                                ->whereKey($get('content_item_id'))
                                ->where('type', ContentType::InternalPost->value)
                                ->exists() ? ['min:70'] : [])
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
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                if ((string) $get('excerpt') !== '') {
                                    return;
                                }

                                $set('excerpt', app(SeoDescription::class)->generateExcerpt((string) $state, (string) $get('title')));
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('External')
                    ->schema([
                        TextInput::make('external_url')
                            ->url()
                            ->maxLength(2048),
                        TextInput::make('external_site_name')
                            ->maxLength(255),
                        TextInput::make('external_og_image_url')
                            ->url()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        TextInput::make('featured_image_url')
                            ->url()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        Textarea::make('external_description')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                if ((string) $get('excerpt') !== '') {
                                    return;
                                }

                                $set('excerpt', app(SeoDescription::class)->generatePlainExcerpt((string) $state, (string) $get('title')));
                            })
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
