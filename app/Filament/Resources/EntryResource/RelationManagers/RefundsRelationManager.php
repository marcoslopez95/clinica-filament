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
        return \App\Filament\Resources\RefundResource\Schemas\RefundForm::configure($form);
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
