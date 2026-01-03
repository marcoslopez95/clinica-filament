<?php

namespace App\Filament\Resources\PatientResource\Schemas;

use App\Models\Patient;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class PatientForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Apellido'),

                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->relationship('typeDocument', 'name'),

                TextInput::make('dni')
                    ->label('Número de Documento'),

                DatePicker::make('born_date')
                    ->label('Fecha de Nacimiento'),

                TextInput::make('address')
                    ->label('Dirección')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Patient $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Patient $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
