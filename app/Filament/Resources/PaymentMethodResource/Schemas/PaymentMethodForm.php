<?php

namespace App\Filament\Resources\PaymentMethodResource\Schemas;

use App\Models\PaymentMethod;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class PaymentMethodForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('description')
                    ->label('Descripción'),

                Select::make('currencies')
                    ->label('Moneda')
                    ->relationship(name: 'currencies', titleAttribute: 'name')
                    ->multiple()
                    ->preload(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?PaymentMethod $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha Última Modificación')
                    ->content(fn(?PaymentMethod $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
