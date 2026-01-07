<?php

namespace App\Filament\Resources\InvoiceResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use App\Enums\InvoiceStatus;
use Filament\Tables\Table;
use \App\Filament\Actions\CancelInvoiceAction;

class InvoicesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('dni')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total')
                    ->label('Total'),

                \App\Filament\Forms\Columns\ToPayColumn::make('balance'),

                \App\Filament\Forms\Columns\StatusColumn::make(),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),

                \App\Filament\Forms\columns\CancellationColumn::make(),
            ])
            ->filters([
                
            ])
            ->actions([
                CancelInvoiceAction::makeTable(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
