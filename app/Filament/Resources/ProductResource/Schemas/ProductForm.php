<?php

namespace App\Filament\Resources\ProductResource\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ProductForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
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

                ...TimestampForm::schema(),
            ]);
    }
}
