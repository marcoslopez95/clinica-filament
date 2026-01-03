<?php

namespace App\Filament\Resources\PaymentMethodResource\Schemas;

use App\Models\PaymentMethod;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Forms\Schemas\SimpleForm;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Form;

class PaymentMethodForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...SimpleForm::schema(),

                Select::make('currencies')
                    ->label('Moneda')
                    ->relationship(name: 'currencies', titleAttribute: 'name')
                    ->multiple()
                    ->preload(),

                ...TimestampForm::schema(),
            ]);
    }
}
