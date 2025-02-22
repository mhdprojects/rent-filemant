<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ContactResource\Pages;
use App\Filament\App\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 5;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Fullname')
                            ->required()
                            ->maxLength(100),

                        PhoneInput::make('phone')
                            ->label('No. Whatsapp')
                            ->required()
                            ->onlyCountries(['id'])
                            ->defaultCountry('id')
                            ->autoInsertDialCode(true)
                            ->inputNumberFormat(PhoneInputNumberType::E164),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->maxLength(200),

                        Forms\Components\Split::make([
                            Toggle::make('is_customer')
                                ->default(true)
                                ->label('As a Customer'),
                            Toggle::make('is_partner')
                                ->default(true)
                                ->label('As a Partner'),
                        ]),

                        Textarea::make('alamat')
                            ->label('Address'),

                        FileUpload::make('image')
                            ->label('Foto Contact')
                            ->disk('public')
                            ->directory('images')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) Str::slug(str($file->getClientOriginalName())
                                        ->prepend(time().'-')).'.'.$file->getClientOriginalExtension(),
                            )
                            ->image()
                            ->maxSize(1024)
                            ->visibility('public'),

                        Textarea::make('description')
                            ->label('Notes'),
                    ])
                    ->columnSpan(['lg' => fn (?Contact $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Contact $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (Contact $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Contact $record) => $record === null),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->height(40)
                    ->width(40)
                    ->disk('public'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Whatsapp')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_customer')
                    ->label('Customer')
                    ->sortable(),

                ToggleColumn::make('is_partner')
                    ->label('Partner')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
