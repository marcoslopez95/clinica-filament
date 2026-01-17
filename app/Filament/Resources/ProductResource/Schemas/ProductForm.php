<?php

namespace App\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Get;

use App\Enums\UnitCategoryEnum;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;

class ProductForm
{
    public static function schema(): array
    {
return [
    TextInput::make('name')
        ->label('Nombre')
        ->required()
        ->unique('products', ignoreRecord: true),

    TextInput::make('buy_price')
        ->label('Precio de Compra')
        ->required()
        ->numeric()
        ->live(),

    TextInput::make('sell_price')
        ->label('Precio de Venta')
        ->required()
        ->numeric()
        ->live(),

    Placeholder::make('profit_margin_display')
        ->label('Porcentaje de Ganancia')
        ->content(function (Get $get) {
            $buyPrice = (float) $get('buy_price');
            $sellPrice = (float) $get('sell_price');

            if ($buyPrice > 0) {
                $profit = (($sellPrice - $buyPrice) / $buyPrice) * 100;
                return number_format($profit, 2) . '%';
            }
            return '0.00%';
        }),

    Select::make('unit_id')
        ->label('Unidad')
        ->required()
        ->options(function () {
            return Unit::whereHas('categories', function (Builder $query) {
                $query->where('unit_categories.id', UnitCategoryEnum::PHARMACY->value);
            })->pluck('name', 'id');
        })
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
