<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ExpenseResource\Pages;
use App\Filament\App\Resources\ExpenseResource\RelationManagers;
use App\Models\Asset;
use App\Models\Contact;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Expense';

    protected static ?int $navigationSort = 2;

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
                                ->content(fn (Expense $record): ?string => $record->created_at?->diffForHumans()),

                            Forms\Components\Placeholder::make('updated_at')
                                ->label('Last modified at')
                                ->content(fn (Expense $record): ?string => $record->updated_at?->diffForHumans()),
                        ])->from('sm'),
                    ])
                    ->hidden(fn (?Expense $record) => $record === null),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\TextInput::make('number')
                                ->label('Number')
                                ->placeholder('No. Transaksi Otomatis')
                                ->readOnly(),

                            Forms\Components\DatePicker::make('tgl')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
                        ])->from('sm'),

                        Forms\Components\Split::make([
                            Forms\Components\Select::make('contact_id')
                                ->label('Contact')
                                ->relationship(
                                    name: 'contact',
                                    titleAttribute: 'name',
                                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->orderBy('name')
                                )
                                ->getOptionLabelFromRecordUsing(function (Contact $record) {
                                    $label = $record->name;
                                    $customer = '';
                                    $partner = '';

                                    if ($record->is_customer){
                                        $customer = " - Customer";
                                    }

                                    if ($record->is_partner){
                                        $customer = " - Partner";
                                    }
                                    return $label.$customer.$partner;
                                })
                                ->searchable(),

                            Forms\Components\Select::make('asset_id')
                                ->label('Asset')
                                ->relationship(
                                    name: 'asset',
                                    titleAttribute: "name",
                                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->where('is_active', true)->orderBy('name')
                                )
                                ->getOptionLabelFromRecordUsing(fn (Asset $record) => "{$record->name} {$record->brand->name} {$record->typeModel->name} {$record->warna} {$record->tahun}")
                                ->searchable(),
                        ])->from('sm'),

                        Forms\Components\Split::make([
                            Forms\Components\Select::make('expense_category_id')
                                ->label('Category')
                                ->relationship(
                                    name: 'expenseCategory',
                                    titleAttribute: 'name',
                                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->orderBy('name')
                                )
                                ->native(false)
                                ->required(),

                            Forms\Components\Select::make('payment_method_id')
                                ->label('Metode Pembayaran')
                                ->relationship(
                                    name: 'paymentMethod',
                                    titleAttribute: 'name',
                                    modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->orderByDesc('is_default')->orderBy('name')
                                )
                                ->native(false)
                                ->required(),
                        ])->from('sm'),

                        Forms\Components\TextInput::make('nominal')
                            ->numeric()
                            ->default(0)
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                            ->required(),

                        FileUpload::make('images')
                            ->label('File Attachment')
                            ->disk('public')
                            ->directory('files')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) Str::slug(str($file->getClientOriginalName())
                                        ->prepend(time().'-')).'.'.$file->getClientOriginalExtension(),
                            )
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'image/png',
                                'image/jpeg',
                            ])
                            ->multiple()
                            ->maxSize(2048)
                            ->visibility('public'),

                        Textarea::make('description')
                            ->label('Catatan'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('No. Trx')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl')
                    ->date()
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expenseCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PaymentMethod.name')
                    ->label('Metode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nominal')
                    ->currency()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
