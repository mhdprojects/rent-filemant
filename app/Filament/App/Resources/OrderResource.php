<?php

namespace App\Filament\App\Resources;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Filament\App\Resources\OrderResource\Pages;
use App\Filament\App\Resources\OrderResource\RelationManagers;
use App\Helper\Constant;
use App\Models\Asset;
use App\Models\AssetVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 8;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Created at')
                                ->content(fn (Order $record): ?string => $record->created_at?->diffForHumans()),

                            Forms\Components\Placeholder::make('updated_at')
                                ->label('Last modified at')
                                ->content(fn (Order $record): ?string => $record->updated_at?->diffForHumans()),
                        ])->from('sm'),
                    ])
                    ->hidden(fn (?Order $record) => $record === null),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('Number')
                            ->placeholder('No. Transaksi Otomatis')
                            ->columnSpan(1)
                            ->readOnly(),

                        Forms\Components\Select::make('contact_id')
                            ->label('Customer')
                            ->relationship(
                                name: 'contact',
                                titleAttribute: 'name',
                                modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->where('is_customer', true)
                            )
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama lengkap')
                                    ->required()
                                    ->maxLength(100),

                                PhoneInput::make('phone')
                                    ->label('No, Whatsapp')
                                    ->required()
                                    ->onlyCountries(['id'])
                                    ->defaultCountry('id')
                                    ->autoInsertDialCode(true)
                                    ->inputNumberFormat(PhoneInputNumberType::E164),

                                Forms\Components\TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->maxLength(200),

                                Forms\Components\Split::make([
                                    Toggle::make('is_customer')
                                        ->default(true)
                                        ->label('Sebagai Customer'),
                                    Toggle::make('is_partner')
                                        ->default(true)
                                        ->label('Sebagai  Partner'),
                                ]),

                                Textarea::make('alamat')
                                    ->label('Alamat'),

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
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Create customer')
                                    ->modalSubmitActionLabel('Create customer')
                                    ->modalWidth('xl');
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\DatePicker::make('tgl')
                            ->label('Tanggal Sewa')
                            ->default(now())
                            ->readOnly(fn (?Order $record) => $record !== null)
                            ->reactive()
                            ->required(),

                        Forms\Components\TimePicker::make('jam')
                            ->label('Jam Sewa')
                            ->seconds(false)
                            ->datalist(Constant::TIME_SELECT)
                            ->reactive()
                            ->displayFormat('H:i:s')
                            ->readOnly(fn (?Order $record) => $record !== null)
                            ->format('H:i:s')
                            ->required(),

                        Forms\Components\ToggleButtons::make('status')
                            ->label('Status Order')
                            ->hidden(fn (?Order $record) => $record === null)
                            ->inline()
                            ->disabled(fn (?Order $record) => $record === null || $record->status === OrderStatus::Partial || $record->status === OrderStatus::Done|| $record->status === OrderStatus::Processing || $record->status === OrderStatus::Cancelled)
                            ->options(OrderStatus::class)
                            ->required(),
                    ])->columns(),

                Forms\Components\Section::make('Detail Pesanan')
                    ->schema([
                        Forms\Components\Repeater::make("items")
                            ->relationship()
                            ->schema(self::detailItems())
                            ->orderColumn('sort')
                            ->defaultItems(1)
                            ->hiddenLabel()
                            ->addable(fn ($record) => $record === null || $record->status === OrderStatus::New)
                            ->deletable(fn ($record) => $record == null || $record->status === OrderStatus::New)
                            ->columns([
                                'md' => 12,
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Total Pembayaran')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder(function (Forms\Get $get, Forms\Set $set) {
                                $fields = $get('items');
                                $sum = 0;
                                foreach($fields as $field){
                                    $sum += $field['subtotal'];
                                }

                                $set('subtotal', $sum);
                                return $sum;
                            })
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                            ->numeric()
                            ->required()
                            ->columnSpan([
                                'md' => 3,
                            ]),
                        Forms\Components\TextInput::make('paid')
                            ->label('Terbayar')
                            ->disabled()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                            ->numeric()
                            ->required()
                            ->hidden(fn (?Order $record) => $record === null)
                            ->columnSpan([
                                'md' => 3,
                            ]),
                        Forms\Components\TextInput::make('kurang')
                            ->label('Sisa Pembayaran')
                            ->disabled()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                            ->numeric()
                            ->required()
                            ->hidden(fn (?Order $record) => $record === null)
                            ->columnSpan([
                                'md' => 3,
                            ]),
                        Textarea::make('description')
                            ->label('Catatan'),
                    ])
                    ->columns(1),
            ])->columns(1);
    }

    private static function detailItems(): array{
        return [
            Forms\Components\Select::make('asset_id')
                ->relationship(
                    name: 'asset',
                    titleAttribute: "name",
                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->where('is_active', true)->orderBy('name')
                )
                ->getOptionLabelFromRecordUsing(fn (Asset $record) => "{$record->name} {$record->brand->name} {$record->typeModel->name} {$record->warna} {$record->tahun}")
                ->searchable()
                ->distinct()
                ->disabled(fn (?OrderItem $record) => $record != null)
                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get){
                    $asset = Asset::query()->find($state);

                    if ($asset){
                        $set('stock', $asset->stock);
                        $set('qty', 1);
                    }
                })
                ->columnSpan([
                    'md' => 7,
                ]),

            Forms\Components\Select::make('asset_variant_id')
                ->relationship(
                    name: 'assetVariant',
                    titleAttribute: "name",
                    modifyQueryUsing:  fn (Builder $query, Forms\Get $get) => $query->where('asset_id', $get('asset_id'))->orderBy('name')
                )
                ->disabled(fn (?OrderItem $record) => $record != null)
                ->reactive()
                ->distinct()
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get){
                    $data = AssetVariant::query()->find($state);

                    if ($data){
                        $set('duration', $data->duration);
                        $set('period_in', $data->period_in);
                        $set('price', $data->price * 1);
                        $set('subtotal', $data->price * $get('qty'));
                    }
                })
                ->columnSpan([
                    'md' => 5,
                ]),

            Forms\Components\Hidden::make('stock'),
            Forms\Components\Hidden::make('duration'),
            Forms\Components\Hidden::make('period_in'),

            Forms\Components\TextInput::make('qty')
                ->label('QTY')
                ->numeric()
                ->hidden(fn (Forms\Get $get) => $get('stock') < 2)
                ->default(1)
                ->reactive()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get){
                    $price = $get('price') ?? 0;
                    if($price > 0){
                        $set('subtotal', $price * $state);
                    }else{
                        $set('subtotal', 0);
                    }
                })
                ->columnSpan([
                    'md' => 3,
                ])
                ->required(),

            Forms\Components\TextInput::make('price')
                ->label('Harga')
                ->disabled()
                ->default(0)
                ->dehydrated()
                ->numeric()
                ->required()
                ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                ->columnSpan([
                    'md' => 3,
                ]),

            Forms\Components\TextInput::make('subtotal')
                ->label('Subtotal')
                ->disabled()
                ->dehydrated()
                ->numeric()
                ->required()
                ->default(0)
                ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                ->columnSpan([
                    'md' => 3,
                ]),

            Forms\Components\DatePicker::make('start_date')
                ->label('Tanggal Mulai Sewa')
                ->default(now())
                ->hidden(fn (?OrderItem $record) => $record === null)
                ->readOnly(fn (?OrderItem $record) => $record !== null)
                ->columnSpan([
                    'md' => 3,
                ])
                ->required(),

            Forms\Components\TimePicker::make('start_time')
                ->label('Jam Mulai Sewa')
                ->seconds(false)
                ->datalist(Constant::TIME_SELECT)
                ->displayFormat('H:i:s')
                ->format('H:i:s')
                ->hidden(fn (?OrderItem $record) => $record === null)
                ->readOnly(fn (?OrderItem $record) => $record !== null)
                ->columnSpan([
                    'md' => 3,
                ])
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Tanggal Selesai Sewa')
                ->readOnly()
                ->hidden(fn (?OrderItem $record) => $record === null)
                ->readOnly(fn (?OrderItem $record) => $record !== null)
                ->columnSpan([
                    'md' => 3,
                ]),
            Forms\Components\TimePicker::make('end_time')
                ->label('Tanggal Selesai Sewa')
                ->readOnly()
                ->seconds(false)
                ->displayFormat('H:i:s')
                ->format('H:i:s')
                ->hidden(fn (?OrderItem $record) => $record === null)
                ->readOnly(fn (?OrderItem $record) => $record !== null)
                ->columnSpan([
                    'md' => 3,
                ]),
            Toggle::make('sudah_kembali')
                ->label('Asset Sudah Dikembalikan')
                ->hidden(fn (?OrderItem $record) => $record === null || $record->status === OrderStatus::Partial || $record->status === OrderStatus::Processing)
                ->columnSpan([
                    'md' => 6,
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('No. Trx')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->currency()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid')
                    ->label('Bayar')
                    ->currency()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::class),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(PaymentStatus::class),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . \Illuminate\Support\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->groups([
                Tables\Grouping\Group::make('tgl')
                    ->label('Tanggal')
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('PDF')
                    ->action(fn(Order $record) => OrderResource::pdf($record))
                    ->icon('heroicon-o-printer')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function pdf(Order $order): \Symfony\Component\HttpFoundation\StreamedResponse{
        $pdf = Pdf::loadView('invoice', ['data' => $order])
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'tempDir' => public_path(),
                'chroot'  => '/Users/mac/Project/MA/rent-webapp/public',
            ]);

        return response()->streamDownload(fn () => print($pdf->output()), "Order-{$order->number}.pdf");
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\HistoriesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
