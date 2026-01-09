<?php

namespace App\Filament\Resources\UserResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('profile_photo_path')
                    ->label('Profile Photo Path'),

                TextColumn::make('current_team_id')
                    ->label('Current Team Id'),

                TextColumn::make('two_factor_confirmed_at')
                    ->label('Two Factor Confirmed At'),

                TextColumn::make('two_factor_recovery_codes')
                    ->label('Two Factor Recovery Codes'),

                TextColumn::make('two_factor_secret')
                    ->label('Two Factor Secret'),

                TextColumn::make('email_verified_at')
                    ->label('Email Verified Date')
                    ->date(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
