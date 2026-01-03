<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CurrencyResource\Schemas\CurrencyForm;
use App\Filament\Resources\CurrencyResource\Tables\CurrenciesTable;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $slug = 'currencies';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Moneda';
    protected static ?string $pluralModelLabel = 'Monedas';
    protected static ?string $navigationLabel = 'Monedas';

    public static function form(Form $form): Form
    {
        return CurrencyForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return CurrenciesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
