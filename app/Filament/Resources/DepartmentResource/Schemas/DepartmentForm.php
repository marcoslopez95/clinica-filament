<?php

namespace App\Filament\Resources\DepartmentResource\Schemas;

use App\Models\Department;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Form;

class DepartmentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...\App\Filament\Forms\Schemas\SimpleForm::schema('departments'),
                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
