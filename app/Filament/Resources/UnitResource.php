<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\Schemas\UnitForm;
use App\Filament\Resources\UnitResource\Tables\UnitsTable;
use App\Models\Unit;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $slug = 'units';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $modelLabel = 'Unidad';
    protected static ?string $pluralModelLabel = 'Unidades';
    protected static ?string $navigationLabel = 'Unidades';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('units.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('units.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('units.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('units.delete');
    }

    public static function form(Form $form): Form
    {
        return UnitForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return UnitsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }


    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
