<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Filament\Resources\ServiceCategoryResource\Schemas\ServiceCategoryForm;
use App\Filament\Resources\ServiceCategoryResource\Tables\ServiceCategoriesTable;
use App\Models\ServiceCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceCategoryResource extends Resource
{
    protected static ?string $model = ServiceCategory::class;


    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $modelLabel = 'Categoría de Servicio';
    protected static ?string $pluralModelLabel = 'Categorías de Servicios';
    protected static ?string $navigationLabel = 'Categorías de Servicios';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('service_categories.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('service_categories.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('service_categories.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('service_categories.delete');
    }

    public static function form(Form $form): Form
    {
        return ServiceCategoryForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ServiceCategoriesTable::table($table);
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
            'index' => Pages\ListServiceCategories::route('/'),
            'create' => Pages\CreateServiceCategory::route('/create'),
            'edit' => Pages\EditServiceCategory::route('/{record}/edit'),
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
