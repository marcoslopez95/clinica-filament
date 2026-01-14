<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitCategoryResource\Pages;
use App\Filament\Resources\UnitCategoryResource\Schemas\UnitCategoryForm;
use App\Filament\Resources\UnitCategoryResource\Tables\UnitCategoriesTable;
use App\Models\UnitCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitCategoryResource extends Resource
{
    protected static ?string $model = UnitCategory::class;

    protected static ?string $slug = 'unit-categories';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $modelLabel = 'Categoría de Unidad';
    protected static ?string $pluralModelLabel = 'Categorías de Unidades';
    protected static ?string $navigationLabel = 'Categorías de Unidades';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('unit_categories.list') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('unit_categories.create') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('unit_categories.edit') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('unit_categories.delete') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return UnitCategoryForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return UnitCategoriesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitCategories::route('/'),
            'create' => Pages\CreateUnitCategory::route('/create'),
            'edit' => Pages\EditUnitCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
