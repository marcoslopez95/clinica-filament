<?php

namespace App\Filament\Resources\CurrencyResource\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class CurrencyForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Nombre')->required(),

                TextInput::make('symbol')->label('Símbolo')->required(),

                TextInput::make('exchange')->label('Tasa de Cambio')->required()->numeric(),

                Select::make('paymentMethods')
                    ->label('Métodos de Pago')
                    ->relationship('paymentMethods', 'name')
                    ->multiple()
                    ->preload(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Currency $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Última Modificación')
                    ->content(fn(?Currency $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
