<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundResource\Pages;
use App\Filament\Resources\RefundResource\Schemas\RefundForm;
use App\Filament\Resources\RefundResource\Tables\RefundsTable;
use App\Models\Refund;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    // hide from navigation
    protected static ?string $navigationIcon = null;

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

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
