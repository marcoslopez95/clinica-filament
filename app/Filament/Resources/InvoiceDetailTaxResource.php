<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceDetailTaxResource\Pages;
use App\Filament\Resources\InvoiceDetailTaxResource\Schemas\InvoiceDetailTaxForm;
use App\Filament\Resources\InvoiceDetailTaxResource\Tables\InvoiceDetailTaxesTable;
use App\Models\InvoiceDetailTax;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetailTaxResource extends Resource
{
    protected static ?string $model = InvoiceDetailTax::class;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return InvoiceDetailTaxForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return InvoiceDetailTaxesTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceDetailTaxes::route('/'),
            'create' => Pages\CreateInvoiceDetailTax::route('/create'),
            'edit' => Pages\EditInvoiceDetailTax::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
