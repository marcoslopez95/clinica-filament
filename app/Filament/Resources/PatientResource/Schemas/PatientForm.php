<?php

namespace App\Filament\Resources\PatientResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class PatientForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('first_name')
                ->label('Nombre')
                ->required(),

            TextInput::make('last_name')
                ->label('Apellido'),

            \App\Filament\Forms\Components\TypeDocumentSelect::make(),

            TextInput::make('dni')
                ->label('Número de Documento'),

            DatePicker::make('born_date')
                ->label('Fecha de Nacimiento'),

            TextInput::make('address')
                ->label('Dirección')
                ->required(),

            ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
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
