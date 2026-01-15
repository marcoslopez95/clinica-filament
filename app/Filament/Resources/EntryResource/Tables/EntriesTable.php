<?php

namespace App\Filament\Resources\EntryResource\Tables;

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
use \App\Filament\Actions\CancelInvoiceAction;

class EntriesTable
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
                    ->label('Monto'),

                TextColumn::make('currency.name')
                    ->label('Moneda'),

                TextColumn::make('exchange')
                    ->label('Tasa de cambio'),

                \App\Filament\Forms\Columns\ToPayColumn::make(),

                \App\Filament\Forms\Columns\StatusColumn::make(),

                TextColumn::make('is_expired')
                    ->label('Condición')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Vencida' : 'Sin vencer')
                    ->sortable(),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),

                \App\Filament\Forms\Columns\CancellationColumn::make(),
            ])
            ->filters([

            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('print')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.invoice', $record))
                    ->openUrlInNewTab(),
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
