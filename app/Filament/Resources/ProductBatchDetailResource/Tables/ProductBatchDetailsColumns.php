<?php

namespace App\Filament\Resources\ProductBatchDetailResource\Tables;

use Filament\Tables\Columns\TextColumn;

class ProductBatchDetailsColumns
{
    public static function columns(): array
    {
        return [
            TextColumn::make('batch_number')
                ->label('Número de Lote')
                ->searchable(),

            TextColumn::make('expiration_date')
                ->label('Vencimiento')
                ->date(),

            TextColumn::make('quantity')
                ->label('Cantidad'),
        ];
    }
}
