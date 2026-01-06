<?php

namespace App\Filament\Resources\RoomResource\Schemas;

use Filament\Forms\Form;

class RoomForm
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
