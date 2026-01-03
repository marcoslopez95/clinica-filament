<?php

namespace App\Filament\Resources\EntryResource\Tables;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EntriesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->sortable()->searchable(),
                TextColumn::make('dni')->sortable()->searchable(),
                TextColumn::make('date')->label('Fecha')->date()->sortable()->searchable(),
                TextColumn::make('total')->label('Monto'),
                TextColumn::make('currency.name')->label('Moneda'),
                TextColumn::make('exchange')->label('Tasa de cambio'),
                TextColumn::make('to_pay_with_discounts')->label('Por Pagar'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn(InvoiceStatus $state): string => $state->getName())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('is_expired')
                    ->label('Condición')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Vencida' : 'Sin vencer')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('Status')
                    ->options(InvoiceStatus::class)
                    ->attribute('status')
            ])
            ->actions([
                TableAction::make('Cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Invoice $record) => $record->update(['status' => InvoiceStatus::CANCELLED]))
                    ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED),
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
