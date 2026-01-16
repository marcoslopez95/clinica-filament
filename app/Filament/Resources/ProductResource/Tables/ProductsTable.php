<?php

namespace App\Filament\Resources\ProductResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;

class ProductsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('buy_price')
                    ->label('Precio de Compra'),

                TextColumn::make('sell_price')
                    ->label('Precio de Venta'),

                TextColumn::make('unit.name')
                    ->label('Unidad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('productCategory.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.name')
                    ->label('Moneda')
                    ->searchable()
                    ->sortable(),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('products.bulk_delete')),
                ]),
            ]);
    }
}
