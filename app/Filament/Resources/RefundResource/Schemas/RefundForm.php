<?php

namespace App\Filament\Resources\RefundResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class RefundForm
{
    public static function schema(): array
    {
        return [
            Select::make('payment_method_id')
                ->label('Método de Pago')
                ->relationship('paymentMethod', 'name')
                ->required()
                ->disabled()
                ->dehydrated(),

            ...\App\Filament\Forms\Schemas\CurrencyForm::schema(),

            TextInput::make('amount')
                ->label('Monto')
                ->numeric()
                ->required()
                ->readOnly(),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            
            Select::make('invoice_id')
                ->label('Factura')
                ->relationship('invoice', 'id')
                ->searchable()
                ->required(),

            ...self::schema(),

            ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}

