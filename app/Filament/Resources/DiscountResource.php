<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\Schemas\DiscountForm;
use App\Filament\Resources\DiscountResource\Tables\DiscountsTable;
use App\Models\InvoiceDiscount;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DiscountResource extends Resource
{
    protected static ?string $model = InvoiceDiscount::class;

    protected static ?string $slug = 'discounts';

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $modelLabel = 'Descuento';
    protected static ?string $pluralModelLabel = 'Descuentos';
    protected static ?string $navigationLabel = 'Descuentos';

    public static function form(Form $form): Form
    {
        return DiscountForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return DiscountsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
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
        return [];
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
