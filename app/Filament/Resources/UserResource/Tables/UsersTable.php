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
                    ->label('Foto'),

                TextColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->formatStateUsing(fn($state) => $state ? 'Verificado' : 'No verificado')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->searchable(),
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
