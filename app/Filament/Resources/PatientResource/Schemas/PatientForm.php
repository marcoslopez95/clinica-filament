<?php

namespace App\Filament\Resources\PatientResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;

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
                ->label('Número de Documento')
                ->required(),

            DatePicker::make('born_date')
                ->label('Fecha de Nacimiento')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    $set('age', $state ? \Carbon\Carbon::parse($state)->age : null);
                }),

            TextInput::make('age')
                ->label('Edad')
                ->disabled()
                ->dehydrated()
                ->required()
                ->numeric(),

            TextInput::make('address')
                ->label('Dirección')
                ->required(),

            TextInput::make('phone')
                ->label('Teléfono')
                ->tel(),

        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),
            \App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
