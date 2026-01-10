<?php

namespace App\Filament\Resources\ProductBatchDetailResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;

class ProductBatchDetailForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('batch_number')
                ->label('Número de Lote')
                ->required(),

            DatePicker::make('expiration_date')
                ->label('Fecha de Vencimiento'),

            TextInput::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->required()
                ->step(1),
        ];
    }
}
