<?php

namespace App\Filament\Resources\NewsletterEditions;

use App\Filament\Resources\NewsletterEditions\Pages\CreateNewsletterEdition;
use App\Filament\Resources\NewsletterEditions\Pages\EditNewsletterEdition;
use App\Filament\Resources\NewsletterEditions\Pages\ListNewsletterEditions;
use App\Filament\Resources\NewsletterEditions\Schemas\NewsletterEditionForm;
use App\Filament\Resources\NewsletterEditions\Tables\NewsletterEditionsTable;
use App\Models\NewsletterEdition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NewsletterEditionResource extends Resource
{
    protected static ?string $model = NewsletterEdition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Newsletter';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Éditions';

    public static function form(Schema $schema): Schema
    {
        return NewsletterEditionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsletterEditionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterEditions::route('/'),
            'create' => CreateNewsletterEdition::route('/create'),
            'edit' => EditNewsletterEdition::route('/{record}/edit'),
        ];
    }
}
