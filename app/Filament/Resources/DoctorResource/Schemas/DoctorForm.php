<?php

namespace App\Filament\Resources\DoctorResource\Schemas;

use App\Models\Doctor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class DoctorForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Apellido')
                    ->required(),

                \App\Filament\Forms\Components\TypeDocumentSelect::make(),

                TextInput::make('dni')
                    ->label('Documento')
                    ->required(),

                DatePicker::make('born_date')
                    ->label('Fecha de Nacimiento')
                    ->required(),

                TextInput::make('cost')
                    ->label('Costo')
                    ->required()
                    ->numeric(),

                Select::make('specialization_id')
                    ->relationship('specialization', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Especialización'),

                \App\Filament\Forms\Schemas\TimestampForm::schema()
            ]);
    }
}
