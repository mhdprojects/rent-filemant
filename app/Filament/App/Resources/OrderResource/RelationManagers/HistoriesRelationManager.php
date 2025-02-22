<?php

namespace App\Filament\App\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
//
            ]);
    }

    public function table(Table $table): Table{
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}
