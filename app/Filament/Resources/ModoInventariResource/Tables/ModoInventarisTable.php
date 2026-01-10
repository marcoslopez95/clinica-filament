<?php

namespace App\Filament\Resources\ModoInventariResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModoInventarisTable
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

                TextColumn::make('amount')
                    ->label('Cantidad'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ]);
    }
}
