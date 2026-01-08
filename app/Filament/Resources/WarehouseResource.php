<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\Schemas\WarehouseForm;
use App\Filament\Resources\WarehouseResource\Tables\WarehousesTable;
use App\Models\Warehouse;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $modelLabel = 'Almacén';
    protected static ?string $pluralModelLabel = 'Almacenes';
    protected static ?string $navigationLabel = 'Almacenes';


    public static function form(Form $form): Form
    {
        return WarehouseForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
