<?php

namespace App\Filament\Resources\InventoryResource\Tables;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use App\Filament\Actions\GenericDeleteAction;
use Filament\Tables\Table;

class InventoriesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stock_min')
                    ->label('Stock Minimo'),

                TextColumn::make('amount')
                    ->label('Cantidad'),

                TextColumn::make('batch')
                    ->label('Lote'),

                TextColumn::make('end_date')
                    ->label('Fecha Expiración')
                    ->date(),

                TextColumn::make('observation')
                    ->label('Observaciones'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])

            ->actions([
                EditAction::make()->label('Editar'),
                GenericDeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Borrado masivo'),
                ]),
            ]);
    }
}
