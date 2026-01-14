<?php

namespace App\Filament\Resources\ReferenceValueResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;

class ReferenceValueForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label(false)
                ->placeholder('Nombre')
                ->required(),

            TextInput::make('min_value')
                ->label(false)
                ->prefix('<')
                ->required()
                ->numeric()
                ->placeholder('mínimo')
                ->extraAttributes(['class' => 'text-center']),

            TextInput::make('max_value')
                ->label(false)
                ->prefix('>')
                ->required()
                ->numeric()
                ->placeholder('máximo')
                ->extraAttributes(['class' => 'text-center']),

            Select::make('unit_id')
                ->label(false)
                ->relationship('unit', 'name')
                ->placeholder('Unidad')
                ->searchable()
                ->preload(),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([

            Select::make('exam_id')
                ->label('Examen')
                ->relationship('exam', 'name')
                ->required()
                ->preload(),

            ...self::schema(),

            \App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
