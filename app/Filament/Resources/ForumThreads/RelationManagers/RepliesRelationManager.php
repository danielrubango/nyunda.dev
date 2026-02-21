<?php

namespace App\Filament\Resources\ForumThreads\RelationManagers;

use App\Models\ForumReply;
use Filament\Actions\Action;
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
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('is_hidden')
                    ->default(false),
                MarkdownEditor::make('body_markdown')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['heading', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->required()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reply')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        IconEntry::make('is_visible')
                            ->label('Visible')
                            ->state(fn (ForumReply $record): bool => ! $record->is_hidden)
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger'),
                        IconEntry::make('is_best_reply')
                            ->label('Best')
                            ->state(fn (ForumReply $record): bool => $this->getOwnerRecord()->best_reply_id === $record->id)
                            ->boolean(),
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
                    ->state(fn (ForumReply $record): bool => ! $record->is_hidden)
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('is_best_reply')
                    ->label('Best')
                    ->state(fn (ForumReply $record): bool => $this->getOwnerRecord()->best_reply_id === $record->id)
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('toggle_visibility')
                        ->label(fn (ForumReply $record): string => $record->is_hidden ? 'Show' : 'Hide')
                        ->requiresConfirmation()
                        ->action(function (ForumReply $record): void {
                            $isCurrentlyHidden = $record->is_hidden;

                            $record->update([
                                'is_hidden' => ! $isCurrentlyHidden,
                                'hidden_at' => $isCurrentlyHidden ? null : now(),
                                'hidden_by_id' => $isCurrentlyHidden ? null : auth()->id(),
                            ]);

                            if (! $isCurrentlyHidden && $this->getOwnerRecord()->best_reply_id === $record->id) {
                                $this->getOwnerRecord()->update([
                                    'best_reply_id' => null,
                                ]);
                            }
                        }),
                    Action::make('mark_best_reply')
                        ->label('Mark best reply')
                        ->visible(fn (ForumReply $record): bool => $this->getOwnerRecord()->best_reply_id !== $record->id)
                        ->requiresConfirmation()
                        ->action(function (ForumReply $record): void {
                            $this->getOwnerRecord()->update([
                                'best_reply_id' => $record->id,
                            ]);
                        }),
                    Action::make('clear_best_reply')
                        ->label('Clear best reply')
                        ->visible(fn (ForumReply $record): bool => $this->getOwnerRecord()->best_reply_id === $record->id)
                        ->requiresConfirmation()
                        ->action(function (): void {
                            $this->getOwnerRecord()->update([
                                'best_reply_id' => null,
                            ]);
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->label('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at');
    }
}
