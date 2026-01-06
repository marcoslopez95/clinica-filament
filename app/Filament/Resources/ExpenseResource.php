<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\Schemas\ExpenseForm;
use App\Filament\Resources\ExpenseResource\Tables\ExpensesTable;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';
    
    protected static ?string $navigationGroup = 'Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $modelLabel = 'Gasto';
    protected static ?string $pluralModelLabel = 'Gastos';
    protected static ?string $navigationLabel = 'Gastos';

    public static function form(Form $form): Form
    {
        return ExpenseForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ExpensesTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description'];
    }
}
