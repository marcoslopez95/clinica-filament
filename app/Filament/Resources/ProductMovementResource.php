<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductMovementResource\Pages;
use App\Filament\Resources\ProductMovementResource\Tables\ProductMovementsTable;
use App\Models\InvoiceDetail;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductMovementResource extends Resource
{
    protected static ?string $model = InvoiceDetail::class;

    protected static ?string $slug = 'product-movements';

    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $modelLabel = 'Movimiento de Producto';
    protected static ?string $pluralModelLabel = 'Movimientos de Productos';
    protected static ?string $navigationLabel = 'Movimientos de Productos';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('reports.list');
    }

    public static function table(Table $table): Table
    {
        return ProductMovementsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductMovements::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('content_type', Product::class)
            ->with(['content', 'invoice.invoiceable', 'invoice.currency', 'invoice.details']);
    }
}
