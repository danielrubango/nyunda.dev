<?php

namespace App\Filament\Resources\ContentItems\RelationManagers;

use App\Models\Comment;
use App\Models\ContentItem;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof ContentItem
            && $ownerRecord->isInternalPost();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                MarkdownEditor::make('body_markdown')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['heading', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_hidden')
                    ->label('Hidden'),
            ])
            ->columns(2);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Commentaire')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        IconEntry::make('is_visible')
                            ->label('Visible')
                            ->state(fn (Comment $record): bool => ! $record->is_hidden)
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                        TextEntry::make('body_markdown')
                            ->label('Contenu')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body_markdown')
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->state(fn (Comment $record): bool => ! $record->is_hidden)
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('body_markdown')
                    ->label('Commentaire')
                    ->markdown()
                    ->limit(120),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('visibility')
                    ->label('Visibility')
                    ->options([
                        'visible' => 'Visible',
                        'hidden' => 'Hidden',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'visible' => $query->where('is_hidden', false),
                            'hidden' => $query->where('is_hidden', true),
                            default => $query,
                        };
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
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
            ->defaultSort('created_at', 'desc');
    }
}
