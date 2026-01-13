<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class UserForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([

                // FileUpload::make('profile_photo_path')
                //     ->label('Foto')
                //     ->disk('public')
                //     ->directory('profile-photos')
                //     ->image()
                //     ->maxSize(5120),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),

                DatePicker::make('email_verified_at')
                    ->label('Email Verified Date'),

                TextInput::make('email')
                    ->label('Correo')
                    ->required(),

                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
            ]);
    }
}
