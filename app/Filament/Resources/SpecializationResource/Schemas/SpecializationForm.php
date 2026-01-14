<?php

namespace App\Filament\Resources\SpecializationResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class SpecializationForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('code')
                    ->label('Código'),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
