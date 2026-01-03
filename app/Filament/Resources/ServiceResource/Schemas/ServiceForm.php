<?php

namespace App\Filament\Resources\ServiceResource\Schemas;

use App\Models\Service;
use Filament\Forms\Components\Placeholder;
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

                Select::make('service_category_id')
                    ->label('Categoría')
                    ->required()
                    ->relationship('serviceCategory', 'name')
                    ->preload(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Service $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Service $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
