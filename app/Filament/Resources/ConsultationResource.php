<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceType;
use App\Filament\Resources\ConsultationResource\Pages;
use App\Filament\Resources\ConsultationResource\RelationManagers;
use App\Filament\Resources\OperatingRoomResource\Schemas\InvoiceForm;
use App\Filament\Resources\OperatingRoomResource\Tables\InvoicesTable;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsultationResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'consultations';

    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $modelLabel = 'Consulta';
    protected static ?string $pluralModelLabel = 'Consultas';
    protected static ?string $navigationLabel = 'Consultas';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('consultations.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('consultations.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('consultations.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('consultations.delete');
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
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
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
        return parent::getEloquentQuery()->where('invoice_type', InvoiceType::CONSULT->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
