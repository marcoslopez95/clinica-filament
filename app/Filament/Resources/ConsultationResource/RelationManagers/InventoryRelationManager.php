<?php

namespace App\Filament\Resources\ConsultationResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

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
                TextInput::make('amount')
                    ->label('Cantidad Actual')
                    ->numeric()
                    ->required(),

                Select::make('warehouse_id')
                    ->label('Almacén')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('stock_min')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->required(),

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
                // No permitir crear desde aquí, ya que se crean en el otro RelationManager
            ])
            ->actions([
                EditAction::make()
                    ->label('Ajustar Inventario')
                    ->visible(fn(): bool => auth()->user()->can('consultations.inventories.edit.view'))
                    ->action(function (Model $record, array $data, $livewire): void {
                        if (!auth()->user()->can('consultations.inventories.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar este elemento')
                                ->danger()
                                ->send();

                            return;
                        }
                    }),
            ])
            ->bulkActions([]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('consultations.inventories.view');
    }
}
