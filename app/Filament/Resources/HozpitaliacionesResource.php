<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use App\Filament\Resources\HozpitaliacionesResource\Pages;
use App\Filament\Resources\HozpitaliacionesResource\RelationManagers;
use App\Filament\Resources\InvoiceResource\Tables\InvoicesTable;
use App\Models\Invoice;
use App\Enums\InvoiceType;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HozpitaliacionesResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'hozpitaliaciones';

    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $modelLabel = 'Hozpitaliación';
    protected static ?string $pluralModelLabel = 'Hozpitaliaciones';
    protected static ?string $navigationLabel = 'Hozpitaliaciones';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('hozpitaliaciones.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('hozpitaliaciones.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('hozpitaliaciones.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('hozpitaliaciones.delete');
    }

    public static function form(Form $form): Form
    {
        // reuse invoice form
        return InvoiceForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        // reuse invoice table
        return InvoicesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHozpitaliaciones::route('/'),
            'create' => Pages\CreateHozpitaliacion::route('/create'),
            'edit' => Pages\EditHozpitaliacion::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\InventoryRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('invoice_type', InvoiceType::HOSPITALIZATION->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
