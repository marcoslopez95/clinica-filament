<?php

namespace App\Filament\Resources\ExamResource\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Models\Currency;

class ExamForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required(),

            TextInput::make('price')
                ->label('Precio')
                ->required()
                ->numeric(),

            \App\Filament\Forms\Components\CurrencySelect::make(),

            // Section::make('')
            //     ->description('Agrega o selecciona valores referenciales para este examen')
            //     ->schema([
            //         Repeater::make('referenceValues')
            //             ->label('Valores Referenciales')
            //             ->schema(\App\Filament\Resources\ReferenceValueResource\Schemas\ReferenceValueForm::schema())
            //             ->columns(3)
            //             ->default([]),
            //     ]),
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
