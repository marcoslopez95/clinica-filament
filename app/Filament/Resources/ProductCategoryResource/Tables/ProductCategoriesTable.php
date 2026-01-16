<?php

namespace App\Filament\Resources\ProductCategoryResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;


use Filament\Tables\Table;

class ProductCategoriesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...\App\Filament\Forms\Tables\SimpleTable::columns(),
                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('product_categories.bulk_delete')),
                ]),
            ]);
    }
}
