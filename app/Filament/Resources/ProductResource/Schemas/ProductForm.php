<?php

namespace App\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ProductForm
{
    public static function schema(): array
    {
return [
    TextInput::make('name')
        ->label('Nombre')
        ->required(),

    TextInput::make('buy_price')
        ->label('Precio de Compra')
        ->required()
        ->numeric(),

    TextInput::make('sell_price')
        ->label('Precio de Venta')
        ->required()
        ->numeric(),

    Select::make('unit_id')
        ->label('Unidad')
        ->required()
        ->options(fn() => \App\Models\Unit::pluck('name', 'id'))
        ->searchable(),

    Select::make('product_id')
        ->label('Producto')
        ->options(fn() => \App\Models\Product::pluck('name', 'id'))
        ->searchable(),

    Select::make('product_category_id')
        ->label('Categoría')
        ->required()
        ->options(fn() => \App\Models\ProductCategory::pluck('name', 'id'))
        ->searchable(),

    \App\Filament\Forms\Components\CurrencySelect::make(),
];

    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),
            \App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
