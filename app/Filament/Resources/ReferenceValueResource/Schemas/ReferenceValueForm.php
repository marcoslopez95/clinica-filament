<?php

namespace App\Filament\Resources\ReferenceValueResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ReferenceValueForm
{
    public static function schema(): array
    {
        return [
            Select::make('exam_id')
                ->label('Examen')
                ->relationship('exam', 'name')
                ->required()
                ->preload(),

            TextInput::make('name')
                ->label('Nombre')
                ->required(),

            TextInput::make('min_value')
                ->label('Valor Mínimo')
                ->required()
                ->numeric(),

            TextInput::make('max_value')
                ->label('Valor Máximo')
                ->required()
                ->numeric(),
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
