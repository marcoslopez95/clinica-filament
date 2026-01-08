<?php

namespace App\Filament\Resources\InvoiceDetailTaxResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetailTaxForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required(),

            TextInput::make('percentage')
                ->label('Porcentaje')
                ->numeric()
                ->step(0.01)
                ->required()
                ->suffix('%')
                ->live(debounce: 500)
                ->afterStateUpdated(function ($state, $set, Model $record) {
                    $price = $record->price ?? 0;
                    $quantity = $record->quantity ?? 0;
                    $percentage = (float)$state;
                    $amount = ($price * $quantity) * ($percentage / 100);
                    $set('amount', round($amount, 2));
                }),

            TextInput::make('amount')
                ->label('Monto')
                ->numeric()
                ->required()
                ->live(debounce: 500)
                ->afterStateUpdated(function ($state, $set, Model $record) {
                    $price = $record->price ?? 0;
                    $quantity = $record->quantity ?? 0;
                    $total = $price * $quantity;
                    $amount = (float)$state;
                    if ($total > 0) {
                        $set('percentage', round(($amount / $total) * 100, 2));
                    }
                }),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),
            ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
