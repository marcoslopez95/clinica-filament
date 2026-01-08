<?php

namespace App\Filament\Resources\ServiceCategoryResource\Schemas;

use Filament\Forms\Form;

class ServiceCategoryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...\App\Filament\Forms\Schemas\SimpleForm::schema(),

                ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
