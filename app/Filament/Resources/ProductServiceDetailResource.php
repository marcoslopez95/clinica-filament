<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductServiceDetailResource\Pages;
use App\Filament\Resources\ProductServiceDetailResource\RelationManagers;
use App\Models\ProductServiceDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductServiceDetailResource extends Resource
{
    protected static ?string $model = ProductServiceDetail::class;

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $modelLabel = 'Producto del servicio';
    protected static ?string $pluralModelLabel = 'Productos de los servicios';
    protected static ?string $navigationLabel = 'Productos de los servicios';
    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('product_service_details.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('product_service_details.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('product_service_details.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('product_service_details.delete');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProductServiceDetails::route('/'),
            'create' => Pages\CreateProductServiceDetail::route('/create'),
            'edit' => Pages\EditProductServiceDetail::route('/{record}/edit'),
        ];
    }
}
