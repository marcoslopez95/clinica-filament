<?php

namespace App\Filament\Resources\InventoryResource\Schemas;

use Filament\Forms\Components\DatePicker;
use App\Models\Product;
use App\Models\Inventory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class InventoryForm
{
    public static function schema(): array
    {
        return [
            Select::make('warehouse_id')
                ->label('Almacén')
                ->relationship('warehouse', 'name')
                ->searchable()
                ->preload()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $set('product_id', null);
                }),

            Select::make('product_id')
                ->label('Producto')
                ->options(fn ($get) => Product::when($get('warehouse_id'), fn($q, $warehouseId)
                    => $q->whereNotIn('id', Inventory::where('warehouse_id', $warehouseId)
                        ->pluck('product_id')->toArray()))->pluck('name', 'id'))
                ->disabled(fn ($get) => ! $get('warehouse_id'))
                ->required()
                ->searchable()
                ->createOptionForm(
                    \App\Filament\Resources\ProductResource\Schemas\ProductForm::schema()
                ),

            TextInput::make('stock_min')
                ->label('Stock Mínimo')
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
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),
        ]);
    }
}
