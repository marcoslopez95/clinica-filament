<?php

namespace App\Filament\Resources\InvoiceResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use App\Filament\Actions\CancelInvoiceAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Filters\StatusFilter;
use Filament\Tables\Table;
use App\Enums\InvoiceStatus;

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
            ])
            ->filters([
                StatusFilter::make(),
            ])
            ->actions([
                CancelInvoiceAction::make(),
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
