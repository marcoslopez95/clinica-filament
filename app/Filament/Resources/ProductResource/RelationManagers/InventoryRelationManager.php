<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
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
                TextInput::make('amount')
                    ->label('Cantidad Actual')
                    ->numeric()
                    ->required()
                    ->default(0),

                Select::make('warehouse_id') 
                    ->label('Almacén')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('stock_min')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->required()
                    ->default(0),

                TextInput::make('batch')
                    ->label('Lote'),

                DatePicker::make('end_date')
                    ->label('Fecha de Vencimiento'),

                TextInput::make('observation')
                    ->label('Observación')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                TextColumn::make('amount')
                    ->label('Cantidad Actual'),

                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->sortable()
                    ->searchable(),

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
                CreateAction::make()
                    ->visible(fn (RelationManager $livewire): bool => 
                        $livewire->getOwnerRecord()->inventory === null && 
                        auth()->user()->can('products.inventories.create')
                    ),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('products.inventories.edit')),
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()->can('products.inventories.delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->can('products.inventories.bulk_delete')),
                ])->visible(fn (): bool => auth()->user()->can('products.inventories.bulk_delete')),
            ]);
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('products.inventories.view');
    }
}