<?php

namespace App\Filament\Resources\ServiceCategoryResource\Schemas;

use App\Models\ServiceCategory;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\SimpleForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ServiceCategoryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...SimpleForm::schema(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?ServiceCategory $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?ServiceCategory $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
