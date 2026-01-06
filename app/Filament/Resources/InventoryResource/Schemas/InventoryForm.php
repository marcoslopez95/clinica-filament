<?php

namespace App\Filament\Resources\InventoryResource\Schemas;

use Filament\Forms\Components\DatePicker;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class InventoryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->required()
                    ->createOptionForm(
                        \App\Filament\Resources\ProductResource\Schemas\ProductForm::schema()
                    ),

                Select::make('warehouse_id')
                    ->label('Almacén')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('stock_min')
                    ->label('Stock Minimo')
                    ->required()
                    ->numeric(),

                TextInput::make('amount')
                    ->label('Cantidad')
                    ->required()
                    ->numeric(),

                TextInput::make('batch')
                    ->label('Lote'),

                DatePicker::make('end_date')
                    ->label('Fecha Expiración'),

                TextInput::make('observation')
                    ->label('Observaciones'),

                ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
