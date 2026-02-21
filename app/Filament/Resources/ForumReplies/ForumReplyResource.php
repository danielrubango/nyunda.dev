<?php

namespace App\Filament\Resources\ForumReplies;

use App\Filament\Resources\ForumReplies\Pages\CreateForumReply;
use App\Filament\Resources\ForumReplies\Pages\EditForumReply;
use App\Filament\Resources\ForumReplies\Pages\ListForumReplies;
use App\Filament\Resources\ForumReplies\Schemas\ForumReplyForm;
use App\Filament\Resources\ForumReplies\Schemas\ForumReplyInfolist;
use App\Filament\Resources\ForumReplies\Tables\ForumRepliesTable;
use App\Models\ForumReply;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForumReplyResource extends Resource
{
    protected static ?string $model = ForumReply::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Community';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return ForumReplyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ForumReplyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumRepliesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumReplies::route('/'),
            'create' => CreateForumReply::route('/create'),
            'edit' => EditForumReply::route('/{record}/edit'),
        ];
    }
}
