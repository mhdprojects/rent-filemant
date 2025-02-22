<?php

namespace App\Filament\App\Resources\OrderResource\RelationManagers;

use App\Helper\AutoCode;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'order_payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\TextInput::make('number')
                                ->label('Number')
                                ->placeholder('No. Transaksi Otomatis')
                                ->readOnly(),

                            Forms\Components\DatePicker::make('tgl')
                                ->label('Date Payment')
                                ->default(now())
                                ->required(),
                        ])->from('sm'),

                        Forms\Components\Select::make('payment_method_id')
                            ->label('Payment Method')
                            ->relationship(
                                name: 'paymentMethod',
                                titleAttribute: 'name',
                                modifyQueryUsing:  fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)->orderByDesc('is_default')->orderBy('name')
                            )
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('nominal')
                            ->label('Amount')
                            ->dehydrated()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 0)
                            ->numeric()
                            ->required(),

                        Textarea::make('description')
                            ->label('Notes'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('tgl')->date()->label('Tanggal'),
                Tables\Columns\TextColumn::make('number')->label('No. Trx'),
                Tables\Columns\TextColumn::make('paymentMethod.name')->label('Metode Pembayaran'),
                Tables\Columns\TextColumn::make('nominal')->currency(),
                Tables\Columns\TextColumn::make('description')->label('Catatan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data){
                        $data['tenant_id']  = Filament::getTenant()->id;
                        $data['number']     = AutoCode::paymentNumber($data['tenant_id']);

                        return $data;
                    })
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshOrders');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshOrders');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshOrders');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (Component $livewire) {
                            $livewire->dispatch('refreshOrders');
                        }),
                ]),
            ]);
    }
}
