<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('post.title')
                    ->label('Post')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
