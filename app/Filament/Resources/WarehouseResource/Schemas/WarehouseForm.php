<?php

namespace App\Filament\Resources\WarehouseResource\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;

class WarehouseForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            
            ...\App\Filament\Forms\Schemas\SimpleForm::schema(),

            TextInput::make('location')
                ->label('Ubicación')
                ->nullable(),
        ]);
    }
}

