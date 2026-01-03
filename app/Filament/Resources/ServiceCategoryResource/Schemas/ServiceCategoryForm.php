<?php

namespace App\Filament\Resources\ServiceCategoryResource\Schemas;

use App\Models\ServiceCategory;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ServiceCategoryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('description')
                    ->label('Descripción'),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?ServiceCategory $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?ServiceCategory $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
