<?php

namespace App\Filament\Resources\ExamResource\Schemas;

use App\Models\Exam;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ExamForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric(),

                Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name')
                    ->required(),

                Section::make('')
                    ->label('Valores Referenciales')
                    ->description('Agrega o selecciona valores referenciales para este examen')
                    ->schema([
                        Repeater::make('reference_values')
                            ->relationship('referenceValues')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),

                                TextInput::make('min_value')
                                    ->label('Valor mínimo')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('max_value')
                                    ->label('Valor máximo')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(3)
                    ]),

                ...TimestampForm::schema(),
            ]);
    }
}
