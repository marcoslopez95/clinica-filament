<?php

namespace App\Filament\Resources\WarehouseResource\Schemas;

use App\Models\Warehouse;
use App\Filament\Forms\Schemas\SimpleForm;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;

class WarehouseForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...SimpleForm::schema(),

            TextInput::make('location')
                ->label('Ubicación')
                ->nullable(),
        ]);
    }
}

