<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\RelationManagers as InvoiceRelationManagers;
use App\Filament\Resources\CotizacionResource\Pages;
use App\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use App\Filament\Resources\InvoiceResource\Tables\InvoicesTable;
use App\Models\Invoice;
use App\Enums\InvoiceType;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class CotizacionResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'cotizaciones';

    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'Cotización';
    protected static ?string $pluralModelLabel = 'Cotizaciones';
    protected static ?string $navigationLabel = 'Cotizaciones';

    public static function form(Form $form): Form
    {
        return InvoiceForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCotizaciones::route('/'),
            'create' => Pages\CreateCotizacion::route('/create'),
            'edit' => Pages\EditCotizacion::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\InvoiceResource\RelationManagers\ProductsRelationManager::class,
            \App\Filament\Resources\InvoiceResource\RelationManagers\InventoryRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('invoice_type', InvoiceType::COTIZACION->value);
    }
}
