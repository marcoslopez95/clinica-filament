<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class UserForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextInput::make('profile_photo_path')
                    ->label('Profile Photo Path'),

                TextInput::make('current_team_id')
                    ->label('Current Team Id')
                    ->integer(),

                TextInput::make('two_factor_confirmed_at')
                    ->label('Two Factor Confirmed At'),

                TextInput::make('two_factor_recovery_codes')
                    ->label('Two Factor Recovery Codes'),

                TextInput::make('two_factor_secret')
                    ->label('Two Factor Secret'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),

                DatePicker::make('email_verified_at')
                    ->label('Email Verified Date'),

                TextInput::make('email')
                    ->label('Email')
                    ->required(),

                TextInput::make('name')
                    ->label('Name')
                    ->required(),
            ]);
    }
}
