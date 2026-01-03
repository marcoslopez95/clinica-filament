<?php

namespace App\Filament\Resources\ServiceCategoryResource\Schemas;

use App\Models\ServiceCategory;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\SimpleForm;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ServiceCategoryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...SimpleForm::schema(),

                ...TimestampForm::schema(),
            ]);
    }
}
