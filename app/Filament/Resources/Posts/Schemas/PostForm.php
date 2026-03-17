<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Content Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // PT-BR Tab
                        Tabs\Tab::make('Português (BR)')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Select::make('theme_preference')
                                    ->options([
                                        'dark' => 'Dark',
                                        'light' => 'Light',
                                        'system' => 'System',
                                    ])
                                    ->default('dark')
                                    ->required(),

                                self::getContentBuilder('content', 'Conteúdo (PT-BR)'),
                            ]),

                        // EN Tab
                        Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Title (EN)')
                                    ->maxLength(255),

                                self::getContentBuilder('content_en', 'Content (EN)'),
                            ]),
                    ]),
            ]);
    }

    public static function getContentBuilder(string $name, string $label): Builder
    {
        return Builder::make($name)
            ->label($label)
            ->blocks([
                Block::make('header')
                    ->label('Header')
                    ->icon('heroicon-m-h1')
                    ->schema([
                        Select::make('level')
                            ->options([
                                'h2' => 'H2',
                                'h3' => 'H3',
                                'h4' => 'H4',
                            ])
                            ->required()
                            ->default('h2'),
                        TextInput::make('value')
                            ->required(),
                    ]),
                
                Block::make('text')
                    ->label('Text')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        RichEditor::make('value')
                            ->required(),
                    ]),
                
                Block::make('image')
                    ->label('Image')
                    ->icon('heroicon-m-photo')
                    ->schema([
                        FileUpload::make('src')
                            ->image()
                            ->directory('cms/posts')
                            ->required(),
                        TextInput::make('caption'),
                    ]),
                
                Block::make('poll')
                    ->label('Poll')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Hidden::make('uuid')
                            ->default(fn () => (string) Str::uuid()),
                        TextInput::make('question')
                            ->required(),
                        Repeater::make('options')
                            ->simple(
                                TextInput::make('text')->required()
                            )
                            ->minItems(2)
                            ->required(),
                    ]),
                
                Block::make('callout')
                    ->label('Callout')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Select::make('style')
                            ->options([
                                'info' => 'Info',
                                'warning' => 'Warning',
                                'tip' => 'Tip',
                                'danger' => 'Danger',
                            ])
                            ->required()
                            ->default('info'),
                        Textarea::make('content')
                            ->required(),
                    ]),
                
                Block::make('code')
                    ->label('Code')
                    ->icon('heroicon-m-code-bracket')
                    ->schema([
                        Select::make('language')
                            ->options([
                                'php' => 'PHP',
                                'javascript' => 'JavaScript',
                                'html' => 'HTML',
                                'css' => 'CSS',
                                'bash' => 'Bash',
                                'json' => 'JSON',
                            ])
                            ->required()
                            ->default('php'),
                        Textarea::make('code')
                            ->rows(10)
                            ->required()
                            ->extraAttributes(['class' => 'font-mono text-sm']),
                    ]),
                
                Block::make('quote')
                    ->label('Quote')
                    ->icon('heroicon-m-chat-bubble-bottom-center-text')
                    ->schema([
                        Textarea::make('text')
                            ->required(),
                        TextInput::make('citation'),
                    ]),
                
                Block::make('divider')
                    ->label('Divider')
                    ->icon('heroicon-m-minus')
                    ->schema([]),
            ])
            ->collapsible()
            ->blockNumbers(false);
    }
}
