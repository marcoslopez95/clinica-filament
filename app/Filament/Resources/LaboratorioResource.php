<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use App\Filament\Resources\LaboratorioResource\Pages;
use App\Filament\Resources\LaboratorioResource\RelationManagers;
use App\Filament\Resources\InvoiceResource\Tables\InvoicesTable;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaboratorioResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'laboratorio';

    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $modelLabel = 'Laboratorio';
    protected static ?string $pluralModelLabel = 'Laboratorios';
    protected static ?string $navigationLabel = 'Laboratorio';

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
            'index' => Pages\ListLaboratorios::route('/'),
            'create' => Pages\CreateLaboratorio::route('/create'),
            'edit' => Pages\EditLaboratorio::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ExamsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('invoice_type', InvoiceType::LABORATORY->value);
    }
}
