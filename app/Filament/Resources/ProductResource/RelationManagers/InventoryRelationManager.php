<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'inventory';

    protected static ?string $modelLabel = 'Inventario';
    protected static ?string $pluralModelLabel = 'Inventarios';
    protected static ?string $title = 'Inventario';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Cantidad Actual')
                    ->numeric()
                    ->required()
                    ->default(0),

                Forms\Components\Select::make('warehouse_id')
                    ->label('Almacén')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('stock_min')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->required()
                    ->default(0),

                Forms\Components\TextInput::make('batch')
                    ->label('Lote'),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Fecha de Vencimiento'),

                Forms\Components\TextInput::make('observation')
                    ->label('Observación')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad Actual'),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock_min')
                    ->label('Stock Mínimo'),
                Tables\Columns\TextColumn::make('batch')
                    ->label('Lote'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Vencimiento')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn (RelationManager $livewire): bool => $livewire->getOwnerRecord()->inventory === null),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
