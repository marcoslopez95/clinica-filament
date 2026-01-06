<?php

namespace App\Filament\Resources\PaymentMethodResource\Schemas;

use Filament\Forms\Components\Select;
use App\Filament\Forms\Schemas\SimpleForm;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Form;

class PaymentMethodForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...\App\Filament\Forms\Schemas\SimpleForm::schema(),

                \App\Filament\Forms\Components\CurrencySelect::make(),

                ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
