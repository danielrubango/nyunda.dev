<?php

namespace App\Filament\Resources\ForumReplies\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ForumReplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reply')
                    ->schema([
                        Select::make('forum_thread_id')
                            ->relationship('forumThread', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
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
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
