<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use App\Filament\Resources\InvoiceResource\Tables\InvoicesTable;
use App\Models\Invoice;
use App\Enums\InvoiceType;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use \App\Filament\Actions\MakeInvoiceAction;
use \App\Filament\Actions\CancelInvoiceAction;
use \Filament\Tables\Actions\EditAction;

class QuotationResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'quotations';

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
        $table = InvoicesTable::table($table);

        return $table->actions([
            ...$table->getActions(),
            MakeInvoiceAction::makeTable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\InvoiceResource\RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('invoice_type', InvoiceType::COTIZACION->value);
    }
}
