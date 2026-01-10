<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModoInventariResource\Pages;
use App\Filament\Resources\ModoInventariResource\Tables\InventoryModesTable;
use App\Models\Inventory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InventoryModeResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $slug = 'modo-inventari';

    protected static ?string $navigationGroup = 'Almacén';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Modo inventari';
    protected static ?string $pluralModelLabel = 'Modo inventari';
    protected static ?string $navigationLabel = 'Modo inventari';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return InventoryModesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryModes::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['product', 'warehouse']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['product.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->product) {
            $details['Product'] = $record->product->name;
        }

        return $details;
    }
}
