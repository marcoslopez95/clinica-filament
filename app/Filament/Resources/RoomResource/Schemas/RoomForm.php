<?php

namespace App\Filament\Resources\RoomResource\Schemas;

use App\Models\Room;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use App\Filament\Forms\Schemas\SimpleForm;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Form;

class RoomForm
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
