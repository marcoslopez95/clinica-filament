<?php

namespace App\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Forms\Schemas\TimestampForm;

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
                ->relationship('unit', 'name')
                ->preload(),

            Select::make('product_id')
                ->label('Producto')
                ->relationship('product', 'name')
                ->preload(),

            Select::make('product_category_id')
                ->label('Categoría')
                ->required()
                ->relationship('productCategory', 'name')
                ->preload(),

            Select::make('currency_id')
                ->label('Moneda')
                ->required()
                ->relationship('currency', 'name')
                ->preload(),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),
            ...TimestampForm::schema(),
        ]);
    }
}
