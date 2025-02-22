<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AssetResource\Pages;
use App\Filament\App\Resources\AssetResource\RelationManagers;
use App\Filament\Exports\AssetExporter;
use App\Helper\Constant;
use App\Models\Asset;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 6;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Split::make([
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Created at')
                                ->content(fn (Asset $record): ?string => $record->created_at?->diffForHumans()),

                            Forms\Components\Placeholder::make('updated_at')
                                ->label('Last modified at')
                                ->content(fn (Asset $record): ?string => $record->updated_at?->diffForHumans()),
                        ])->from('sm'),
                    ])
                    ->hidden(fn (?Asset $record) => $record === null),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Asset')
                            ->required()
                            ->maxLength(200),

                        Split::make([
                            Forms\Components\Select::make('brand_id')
                                ->relationship(
                                    name: 'brand',
                                    titleAttribute: 'name',
                                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)
                                )
                                ->searchable()
                                ->required(),
                            Forms\Components\Select::make('type_model_id')
                                ->relationship(
                                    name: 'typeModel',
                                    titleAttribute: 'name',
                                    modifyQueryUsing:  fn (Builder $query, Forms\Get $get) => $query->where('tenant_id', Filament::getTenant()->id)->where('brand_id', $get('brand_id'))
                                )
                                ->searchable()
                                ->required(),
                        ])->from('sm'),

                        Split::make([
                            Forms\Components\TextInput::make('warna')
                                ->label('Warna')
                                ->placeholder('Hitam')
                                ->maxLength(30),

                            Forms\Components\TextInput::make('tahun')
                                ->label('Tahun')
                                ->placeholder('2022')
                                ->required()
                                ->maxLength(4),

                            Forms\Components\TextInput::make('stock')
                                ->label('Jml. tersedia')
                                ->placeholder('1')
                                ->default(1)
                                ->numeric(),
                        ])->from('sm'),

                        Split::make([
                            Toggle::make('is_partner')
                                ->reactive()
                                ->label('Asset bukan milik sendiri'),

                            Forms\Components\Select::make('contact_id')
                                ->relationship(
                                    name: 'contact',
                                    titleAttribute: 'name',
                                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->where('is_partner', true)
                                )
                                ->hidden(fn (Forms\Get $get) => $get('is_partner') === false)
                                ->required(fn (Forms\Get $get) => $get('is_partner'))
                                ->searchable(),
                        ])->from('sm'),

                        FileUpload::make('images')
                            ->disk('public')
                            ->directory('images')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) Str::slug(str($file->getClientOriginalName())
                                        ->prepend(time().'-')).'.'.$file->getClientOriginalExtension(),
                            )
                            ->multiple()
                            ->image()
                            ->maxSize(1024)
                            ->visibility('public'),

                        Textarea::make('description')
                            ->label('Catatan'),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Asset Aktif'),
                    ]),

                Forms\Components\Section::make('Variant Harga')
                    ->schema([
                        Forms\Components\Repeater::make("variants")
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Variant')
                                    ->required()
                                    ->columnSpan([
                                        'md' => 4,
                                    ])
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('duration')
                                    ->label('Durasi')
                                    ->minValue(1)
                                    ->numeric()
                                    ->required()
                                    ->columnSpan([
                                        'md' => 2,
                                    ]),
                                Forms\Components\Select::make('period_in')
                                    ->label('Periode')
                                    ->options([
                                        Constant::PERIOD_MINUTE => Constant::PERIOD_MINUTE,
                                        Constant::PERIOD_HOUR => Constant::PERIOD_HOUR,
                                        Constant::PERIOD_DAY => Constant::PERIOD_DAY,
                                    ])
                                    ->placeholder('Pilih')
                                    ->columnSpan([
                                        'md' => 3,
                                    ])
                                    ->native(false),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                                    ->required()
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),
                            ])
                            ->orderColumn('sort')
                            ->defaultItems(1)
                            ->hiddenLabel()
                            ->columns([
                                'md' => 12,
                            ])
                            ->required(),
                    ]),
            ])
            ->columns(1);
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
                Tables\Columns\TextColumn::make('typeModel.name')
                    ->label('Type Model')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('warna')
                    ->label('Warna')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->date()
                    ->toggleable()
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
            ->headerActions([
                Tables\Actions\ExportAction::make()->exporter(AssetExporter::class),
            ])
            ->groups([
                Tables\Grouping\Group::make('brand.name')
                    ->label('Brand')
                    ->collapsible(),
                Tables\Grouping\Group::make('typeModel.name')
                    ->label('Type Model')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
