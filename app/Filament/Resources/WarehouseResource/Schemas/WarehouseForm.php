<?php

namespace App\Filament\Resources\WarehouseResource\Schemas;

use App\Models\Warehouse;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class WarehouseForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('location')
                    ->label('Ubicación')
                    ->nullable(),

                Textarea::make('description')
                    ->label('Descripción')
                    ->nullable(),
            ]);
    }
}
