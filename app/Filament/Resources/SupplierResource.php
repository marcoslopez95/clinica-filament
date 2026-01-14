<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\Schemas\SupplierForm;
use App\Filament\Resources\SupplierResource\Tables\SuppliersTable;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $pluralModelLabel = 'Proveedores';
    protected static ?string $navigationLabel = 'Proveedores';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('suppliers.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('suppliers.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('suppliers.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('suppliers.delete');
    }

    public static function form(Form $form): Form
    {
        return SupplierForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::table($table);
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
