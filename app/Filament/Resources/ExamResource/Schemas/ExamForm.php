<?php

namespace App\Filament\Resources\ExamResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class ExamForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->unique('exams', ignoreRecord: true),

            Select::make('exam_category_id')
                ->relationship('examCategory', 'name')
                ->label('Categoría')
                ->searchable()
                ->preload(),

            TextInput::make('price')
                ->label('Precio')
                ->required()
                ->numeric(),

            \App\Filament\Forms\Components\CurrencySelect::make(),
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
