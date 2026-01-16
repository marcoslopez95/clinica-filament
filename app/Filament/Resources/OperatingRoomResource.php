<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\OperatingRoomResource\Pages;
use App\Filament\Resources\OperatingRoomResource\RelationManagers;
use App\Filament\Resources\OperatingRoomResource\Schemas\InvoiceForm;
use App\Filament\Resources\OperatingRoomResource\Tables\InvoicesTable;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Patient;
use App\Enums\InvoiceType;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OperatingRoomResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'operating-room';

    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $modelLabel = 'Quirofano';
    protected static ?string $pluralModelLabel = 'Quirofanos';
    protected static ?string $navigationLabel = 'Quirofanos';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('invoices.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('invoices.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('invoices.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('invoices.delete');
    }

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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('invoice_type', InvoiceType::DEFAULT->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
