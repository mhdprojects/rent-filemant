<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TypeModelResource\Pages;
use App\Filament\App\Resources\TypeModelResource\RelationManagers;
use App\Models\TypeModel;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeModelResource extends Resource
{
    protected static ?string $model = TypeModel::class;

    protected static ?string $navigationLabel = 'Type Model';

    protected static ?string $label = 'Type Model';

    protected static ?string $pluralLabel = 'Type Model';

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('brand_id')
                            ->relationship(
                                name: 'brand',
                                titleAttribute: 'name',
                                modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)
                            )
                            ->required()
                            ->searchable(),
                    ])
                    ->columnSpan(['lg' => fn (?TypeModel $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (TypeModel $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (TypeModel $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?TypeModel $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('brand')
                    ->relationship(name: 'brand', titleAttribute: 'name', modifyQueryUsing: fn(Builder $query) => $query->where('tenant_id', Filament::getTenant()->id))
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->groups([
                Tables\Grouping\Group::make('brand.name')
                    ->label('Brand')
                    ->collapsible(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypeModels::route('/'),
            'create' => Pages\CreateTypeModel::route('/create'),
            'edit' => Pages\EditTypeModel::route('/{record}/edit'),
        ];
    }
}
