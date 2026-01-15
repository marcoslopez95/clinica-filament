<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceReportResource\Pages;
use App\Filament\Resources\InvoiceReportResource\Tables\InvoiceReportsTable;
use App\Models\Invoice;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceReportResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'invoice-reports';

    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $modelLabel = 'Reporte de Factura';
    protected static ?string $pluralModelLabel = 'Reportes de Facturas';
    protected static ?string $navigationLabel = 'Reportes de Facturas';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('reports.list');
    }

    public static function table(Table $table): Table
    {
        return InvoiceReportsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceReports::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['invoiceable', 'currency', 'details']);
    }
}
