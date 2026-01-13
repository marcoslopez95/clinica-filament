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

            TextInput::make('name')
                ->label('Nombre')
                ->required(),

            ...self::schema(),
            
            ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
