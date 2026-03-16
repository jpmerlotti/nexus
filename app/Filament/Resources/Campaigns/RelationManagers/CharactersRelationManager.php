<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CharactersRelationManager extends RelationManager
{
    protected static string $relationship = 'characters';

    public function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\Characters\Schemas\CharacterForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('race')->searchable(),
                TextColumn::make('class')->searchable(),
                TextColumn::make('level')->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();

                    return $data;
                }),
                AttachAction::make()->recordSelectOptionsQuery(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('user_id', auth()->id())),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
