<?php

namespace App\Filament\Resources\QuotationResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use \App\Filament\Actions\MakeInvoiceAction;
use \App\Filament\Actions\CancelInvoiceAction;

class QuotationsTable
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

                TextColumn::make('credit_date')
                    ->label('Vigencia')
                    ->date()
                    ->sortable(),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])
            ->filters([
            ])
            ->actions([
                CancelInvoiceAction::makeTable(),
                MakeInvoiceAction::makeTable(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
