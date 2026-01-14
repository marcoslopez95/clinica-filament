<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Filament\Resources\ProductCategoryResource\Schemas\ProductCategoryForm;
use App\Filament\Resources\ProductCategoryResource\Tables\ProductCategoriesTable;
use App\Models\ProductCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $slug = 'product-categories';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $modelLabel = 'Categoría de Producto';
    protected static ?string $pluralModelLabel = 'Categorías de Productos';
    protected static ?string $navigationLabel = 'Categorías de Productos';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('product_categories.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('product_categories.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('product_categories.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('product_categories.delete');
    }

    public static function form(Form $form): Form
    {
        return ProductCategoryForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ProductCategoriesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
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
