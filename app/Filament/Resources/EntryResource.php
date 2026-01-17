<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntryResource\Pages;
use App\Models\Invoice;
use App\Enums\InvoiceType;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EntryResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\EntryResource\RelationManagers\InventoryRelationManager;
use App\Filament\Resources\EntryResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\EntryResource\RelationManagers\DiscountsRelationManager;
use App\Filament\Resources\EntryResource\RelationManagers\RefundsRelationManager;
use App\Filament\Resources\EntryResource\Schemas\EntryForm;
use App\Filament\Resources\EntryResource\Tables\EntriesTable;

class EntryResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'entries';

    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $modelLabel = 'Entrada';
    protected static ?string $pluralModelLabel = 'Entradas';
    protected static ?string $navigationLabel = 'Entradas';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('entries.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('entries.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('entries.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('entries.delete');
    }

    public static function form(Form $form): Form
    {
        return EntryForm::configure($form);
    }


    public static function table(Table $table): Table
    {
        return EntriesTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
            InventoryRelationManager::class,
            PaymentsRelationManager::class,
            DiscountsRelationManager::class,
            RefundsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntries::route('/'),
            'create' => Pages\CreateEntry::route('/create'),
            'edit' => Pages\EditEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('invoice_type', InvoiceType::INVENTORY->value)
            ->where('is_quotation', false);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
