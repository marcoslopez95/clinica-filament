<?php

namespace App\Filament\Resources\ServiceResource\Schemas;

use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ServiceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique(ignorable: true),

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

                Select::make('service_category_id')
                    ->label('Categoría')
                    ->required()
                    ->relationship('serviceCategory', 'name')
                    ->preload(),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
