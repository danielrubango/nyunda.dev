<?php

namespace App\Filament\Resources\NewsletterEditions\Schemas;

use App\Enums\ContentType;
use App\Models\ContentItem;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterEditionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sujets')
                    ->columns(2)
                    ->schema([
                        TextInput::make('subject_fr')
                            ->label('Sujet (FR)')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('subject_en')
                            ->label('Subject (EN)')
                            ->required()
                            ->maxLength(150),
                    ]),

                Section::make('Introduction')
                    ->columns(2)
                    ->schema([
                        Textarea::make('intro_fr')
                            ->label('Introduction (FR)')
                            ->rows(3)
                            ->maxLength(500),
                        Textarea::make('intro_en')
                            ->label('Introduction (EN)')
                            ->rows(3)
                            ->maxLength(500),
                    ]),

                Section::make('Articles sélectionnés')
                    ->description('Choisissez les articles publiés à inclure dans cette édition.')
                    ->schema([
                        CheckboxList::make('content_item_ids')
                            ->label('Articles')
                            ->default([])
                            ->options(function (): array {
                                return ContentItem::query()
                                    ->published()
                                    ->whereIn('type', [
                                        ContentType::InternalPost->value,
                                        ContentType::ExternalPost->value,
                                    ])
                                    ->with('translations')
                                    ->latest('published_at')
                                    ->take(50)
                                    ->get()
                                    ->mapWithKeys(function (ContentItem $item): array {
                                        $translation = $item->translations
                                            ->firstWhere('locale', 'fr')
                                            ?? $item->translations->first();

                                        $label = $translation?->title ?? "(#{$item->id} sans titre)";
                                        $type = $item->type->value === 'internal_post' ? '📝' : '🔗';

                                        return [$item->id => "{$type} {$label}"];
                                    })
                                    ->all();
                            })
                            ->columns(1)
                            ->searchable(),
                    ]),
            ]);
    }
}
