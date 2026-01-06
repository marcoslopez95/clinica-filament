<?php

namespace App\Filament\Resources\ProductCategoryResource\Schemas;

use Filament\Forms\Form;

class ProductCategoryForm
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
