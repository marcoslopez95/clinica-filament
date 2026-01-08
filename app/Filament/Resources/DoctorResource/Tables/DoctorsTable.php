<?php

namespace App\Filament\Resources\DoctorResource\Tables;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Table;

class DoctorsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Nombre'),

                TextColumn::make('last_name')
                    ->label('Apellido'),

                TextColumn::make('typeDocument.name')
                    ->label('Tipo de Documento')
                    ->sortable(),

                TextColumn::make('dni')
                    ->label('Num. Documento'),

                TextColumn::make('born_date')
                    ->label('Fecha de Nacimiento')
                    ->date(),
                    
                TextColumn::make('cost')
                    ->label('Costo'),
                    
                TextColumn::make('specialization.name')
                    ->label('Especialización'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])
            
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
