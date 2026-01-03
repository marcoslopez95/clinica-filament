<?php

namespace App\Filament\Resources\InventoryResource\Schemas;

use App\Models\Inventory;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                    ->createOptionForm([
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
                    ]),

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

                TextInput::make('observation')->label('Observaciones'),

                ...TimestampForm::schema(),
            ]);
    }
}
