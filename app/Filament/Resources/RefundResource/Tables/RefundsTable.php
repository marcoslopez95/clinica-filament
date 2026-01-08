<?php

namespace App\Filament\Resources\RefundResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Table;

class RefundsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_id')->label('Pago Original')->formatStateUsing(fn($state) => "#{$state}"),
                TextColumn::make('paymentMethod.name')->label('Método de Pago'),
                TextColumn::make('currency.name')->label('Moneda'),
                TextColumn::make('amount')->label('Monto'),
                TextColumn::make('exchange')->label('Tasa de Cambio'),
                TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
