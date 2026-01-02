<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use App\Models\Currency;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;

class RefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'refunds';

    protected static ?string $title = 'Devoluciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('payment_id')
                    ->label('Pago Original')
                    ->relationship('payment', 'id', modifyQueryUsing: function (Builder $query) {
                        return $query->where('invoice_id', $this->getOwnerRecord()->id)
                            ->whereDoesntHave('refund');
                    })
                    ->getOptionLabelFromRecordUsing(fn (Payment $record) => "Pago {$record->amount} {$record->currency->code} ({$record->paymentMethod->name})")
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $payment = Payment::find($state);
                            if ($payment) {
                                $set('currency_id', $payment->currency_id);
                                $set('payment_method_id', $payment->payment_method_id);
                                $set('exchange', $payment->exchange);
                                $set('amount', $payment->amount);
                            }
                        }
                    }),

                Forms\Components\Select::make('payment_method_id')
                    ->label('Método de Pago')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name')
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->required()
                    ->readOnly(),

                Forms\Components\TextInput::make('exchange')
                    ->label('Tasa de Cambio')
                    ->numeric()
                    ->required()
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('Pago Original')
                    ->formatStateUsing(fn ($state) => "#{$state}"),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago'),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label('Moneda'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money(fn($record) => $record->currency->code ?? 'USD'),
                Tables\Columns\TextColumn::make('exchange')
                    ->label('Tasa de Cambio'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function ($livewire) {
                            $livewire->dispatch('refreshTotal');
                        }),
                ]),
            ]);
    }
}
