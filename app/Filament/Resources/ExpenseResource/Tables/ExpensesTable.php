<?php

namespace App\Filament\Resources\ExpenseResource\Tables;

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

class ExpensesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),

                TextColumn::make('price')
                    ->label('Precio')
                    ->sortable(),

                TextColumn::make('currency.name')
                    ->label('Moneda')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),

                TextColumn::make('exchange')
                    ->label('Tasa de Cambio')
                    ->sortable(),
            ])
            
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
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
