<?php

namespace backup;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Filament\App\Resources\EmployeeResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Employee';

    protected static ?string $label = 'Employee';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('email')
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->required()
                            ->email()
                            ->maxLength(200),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->rule(Password::default())
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->same('passwordConfirmation'),

                        TextInput::make('passwordConfirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->dehydrated(false),
                    ])
                    ->columnSpan(['lg' => fn (?User $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?User $record) => $record === null),
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

                Tables\Columns\TextColumn::make('email')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => \backup\EmployeeResource\Pages\ListEmployees::route('/'),
            'create' => \backup\EmployeeResource\Pages\CreateEmployee::route('/create'),
            'edit' => \backup\EmployeeResource\Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
