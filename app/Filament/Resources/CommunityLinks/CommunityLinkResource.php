<?php

namespace App\Filament\Resources\CommunityLinks;

use App\Enums\ContentType;
use App\Filament\Resources\CommunityLinks\Pages\CreateCommunityLink;
use App\Filament\Resources\CommunityLinks\Pages\EditCommunityLink;
use App\Filament\Resources\CommunityLinks\Pages\ListCommunityLinks;
use App\Filament\Resources\CommunityLinks\Pages\ViewCommunityLink;
use App\Filament\Resources\ContentItems\RelationManagers\TranslationsRelationManager;
use App\Filament\Resources\ContentItems\Schemas\ContentItemForm;
use App\Filament\Resources\ContentItems\Schemas\ContentItemInfolist;
use App\Filament\Resources\ContentItems\Tables\ContentItemsTable;
use App\Models\ContentItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommunityLinkResource extends Resource
{
    protected static ?string $model = ContentItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Editorial';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Community links';
    }

    public static function form(Schema $schema): Schema
    {
        return ContentItemForm::configure($schema, ContentType::CommunityLink);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentItemsTable::configure($table, ContentType::CommunityLink);
    }

    public static function getRelations(): array
    {
        return [
            TranslationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommunityLinks::route('/'),
            'create' => CreateCommunityLink::route('/create'),
            'view' => ViewCommunityLink::route('/{record}'),
            'edit' => EditCommunityLink::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', ContentType::CommunityLink->value);
    }
}
