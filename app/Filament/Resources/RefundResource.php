<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundResource\Pages;
use App\Filament\Resources\RefundResource\Schemas\RefundForm;
use App\Filament\Resources\RefundResource\Tables\RefundsTable;
use App\Models\Refund;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    protected static ?string $slug = 'refunds';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return RefundForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return RefundsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefund::route('/create'),
            'edit' => Pages\EditRefund::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Refund>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['currency', 'paymentMethod']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['currency.name', 'paymentMethod.name'];
    }

    /**
     * @param Refund $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->currency) {
            $details['Currency'] = $record->currency->name;
        }

        if ($record->paymentMethod) {
            $details['PaymentMethod'] = $record->paymentMethod->name;
        }

        return $details;
    }
}
