<?php

namespace App\Filament\Resources\ExternalPosts;

use App\Enums\ContentType;
use App\Filament\Resources\ContentItems\RelationManagers\TranslationsRelationManager;
use App\Filament\Resources\ContentItems\Schemas\ContentItemForm;
use App\Filament\Resources\ContentItems\Schemas\ContentItemInfolist;
use App\Filament\Resources\ContentItems\Tables\ContentItemsTable;
use App\Filament\Resources\ExternalPosts\Pages\CreateExternalPost;
use App\Filament\Resources\ExternalPosts\Pages\EditExternalPost;
use App\Filament\Resources\ExternalPosts\Pages\ListExternalPosts;
use App\Filament\Resources\ExternalPosts\Pages\ViewExternalPost;
use App\Models\ContentItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExternalPostResource extends Resource
{
    protected static ?string $model = ContentItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTopRightOnSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Editorial';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'External posts';
    }

    public static function form(Schema $schema): Schema
    {
        return ContentItemForm::configure($schema, ContentType::ExternalPost);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentItemsTable::configure($table, ContentType::ExternalPost);
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
            'index' => ListExternalPosts::route('/'),
            'create' => CreateExternalPost::route('/create'),
            'view' => ViewExternalPost::route('/{record}'),
            'edit' => EditExternalPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', ContentType::ExternalPost->value);
    }
}
