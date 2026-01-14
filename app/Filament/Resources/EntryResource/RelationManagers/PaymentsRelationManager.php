<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Pagos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Por Pagar')
                ->schema([
                    Placeholder::make('per_pay')
                        ->label('')
                        ->content(fn(RelationManager $livewire): string => $livewire->ownerRecord->balance),
                ]),
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->label('Método de Pago')
                    ->required()
                    ->live(),

                Select::make('currency_id')
                    ->relationship(
                        'currency',
                        'name',
                        modifyQueryUsing: fn(Get $get, Builder $query) => $query
                            ->whereRelation(
                                'paymentMethods',
                                'payment_methods.id',
                                $get('payment_method_id')
                            )
                    )
                    ->label('Moneda')
                    ->disabled(fn(Get $get) => !$get('payment_method_id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, mixed $state) {
                        $currency = Currency::find($state);
                        $set('exchange', $currency->exchange ?? 0);
                    }),

                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->required()
                    ->disabled(fn(Get $get) => !$get('currency_id'))
                    ->live(debounce: 500),

                TextInput::make('exchange')
                    ->label('Tasa de Cambio')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
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
                //
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
