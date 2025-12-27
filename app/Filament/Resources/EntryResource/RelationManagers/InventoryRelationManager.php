<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                Forms\Components\TextInput::make('amount')
                    ->label('Cantidad Actual')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('stock_min')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->required(),

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
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad Actual'),
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
                // No permitir crear desde aquí, ya que se crean en el otro RelationManager
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ajustar Inventario'),
            ])
            ->bulkActions([
            ]);
    }
}
