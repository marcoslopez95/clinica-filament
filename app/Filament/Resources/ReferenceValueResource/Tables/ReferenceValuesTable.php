<?php

namespace App\Filament\Resources\ReferenceValueResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;

class ReferenceValuesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exam.name')
                    ->label('Exam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('min_value')
                    ->label('Min Value')
                    ->sortable(),

                TextColumn::make('max_value')
                    ->label('Max Value')
                    ->sortable(),

                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable()
                    ->searchable(),

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
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('reference_values.bulk_delete')),
                ]),
            ]);
    }
}
