<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\EntryResource\Pages;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\TypeDocument;
use App\Enums\InvoiceType;
use Filament\Forms\Components\Hidden;
use App\Models\Product;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('invoice_type', InvoiceType::INVENTORY->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
