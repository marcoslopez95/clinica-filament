<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Actions\RefreshTotalCreateAction;
use App\Filament\Actions\RefreshTotalEditAction;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Filament\Actions\RefreshTotalDeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;

class RefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'refunds';

    protected static ?string $title = 'Devoluciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('payment_method_id')
                    ->label('Método de Pago')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name')
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->required()
                    ->readOnly(),

                TextInput::make('exchange')
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
                TextColumn::make('payment_id')
                    ->label('Pago Original')
                    ->formatStateUsing(fn ($state) => "#{$state}"),

                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago'),

                TextColumn::make('currency.name')
                    ->label('Moneda'),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money(fn($record) => $record->currency->code ?? 'USD'),

                TextColumn::make('exchange')
                    ->label('Tasa de Cambio'),
                    
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            
            ->headerActions([
                RefreshTotalCreateAction::make(),
            ])
            ->actions([
                RefreshTotalEditAction::make(),
                RefreshTotalDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    RefreshTotalDeleteBulkAction::make(),
                ]),
            ]);
    }
}
