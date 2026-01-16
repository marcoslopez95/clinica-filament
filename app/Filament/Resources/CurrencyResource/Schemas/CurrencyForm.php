<?php

namespace App\Filament\Resources\CurrencyResource\Schemas;

use App\Models\Currency;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class CurrencyForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique('currencies', ignoreRecord: true),

                TextInput::make('symbol')
                    ->label('Símbolo')
                    ->required()
                    ->unique('currencies', ignoreRecord: true),

                TextInput::make('exchange')
                    ->label('Tasa de Cambio')
                    ->required()
                    ->numeric()
                    ->disabled(fn (callable $get) => $get('is_main'))
                    ->dehydrated(),

                Toggle::make('is_main')
                    ->label('Moneda Principal')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('exchange', 1);

                            Notification::make()
                                ->title('La tasa de cambio se ha ajustado a 1')
                                ->body('Al marcar esta moneda como principal, su tasa de cambio se establece automáticamente en 1.')
                                ->warning()
                                ->send();
                        }
                    })
                    ->default(false),

                Select::make('paymentMethods')
                    ->label('Métodos de Pago')
                    ->relationship('paymentMethods', 'name')
                    ->multiple()
                    ->preload(),

                TimestampForm::schema(),
            ]);
    }
}
