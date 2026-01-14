<?php

namespace App\Filament\Resources\HozpitaliacionesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class InventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'inventories';

    protected static ?string $modelLabel = 'Inventario';
    protected static ?string $pluralModelLabel = 'Inventarios';
    protected static ?string $title = 'Control de Inventario';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('warehouse_id')
                    ->label('Almacén')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                ...\App\Filament\Resources\InventoryResource\Schemas\InventoryForm::schema(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Cantidad Actual'),
                TextColumn::make('stock_min')
                    ->label('Stock Mínimo'),
                TextColumn::make('batch')
                    ->label('Lote'),
                TextColumn::make('end_date')
                    ->label('Vencimiento')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
