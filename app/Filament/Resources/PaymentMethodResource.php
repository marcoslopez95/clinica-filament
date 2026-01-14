<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Filament\Resources\PaymentMethodResource\Schemas\PaymentMethodForm;
use App\Filament\Resources\PaymentMethodResource\Tables\PaymentMethodsTable;
use App\Models\PaymentMethod;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $slug = 'payment-methods';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $modelLabel = 'Método de Pago';
    protected static ?string $pluralModelLabel = 'Métodos de Pago';
    protected static ?string $navigationLabel = 'Métodos de Pago';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('payment_methods.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('payment_methods.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('payment_methods.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('payment_methods.delete');
    }

    public static function form(Form $form): Form
    {
        return PaymentMethodForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return PaymentMethodsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
