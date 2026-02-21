<?php

namespace App\Filament\Resources\InternalPosts;

use App\Enums\ContentType;
use App\Filament\Resources\ContentItems\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\ContentItems\RelationManagers\TranslationsRelationManager;
use App\Filament\Resources\ContentItems\Schemas\ContentItemForm;
use App\Filament\Resources\ContentItems\Schemas\ContentItemInfolist;
use App\Filament\Resources\ContentItems\Tables\ContentItemsTable;
use App\Filament\Resources\InternalPosts\Pages\CreateInternalPost;
use App\Filament\Resources\InternalPosts\Pages\EditInternalPost;
use App\Filament\Resources\InternalPosts\Pages\ListInternalPosts;
use App\Filament\Resources\InternalPosts\Pages\ViewInternalPost;
use App\Models\ContentItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InternalPostResource extends Resource
{
    protected static ?string $model = ContentItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Editorial';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Internal posts';
    }

    public static function form(Schema $schema): Schema
    {
        return ContentItemForm::configure($schema, ContentType::InternalPost);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentItemsTable::configure($table, ContentType::InternalPost);
    }

    public static function getRelations(): array
    {
        return [
            TranslationsRelationManager::class,
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternalPosts::route('/'),
            'create' => CreateInternalPost::route('/create'),
            'view' => ViewInternalPost::route('/{record}'),
            'edit' => EditInternalPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', ContentType::InternalPost->value);
    }
}
