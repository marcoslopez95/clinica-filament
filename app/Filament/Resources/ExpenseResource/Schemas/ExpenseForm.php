<?php

namespace App\Filament\Resources\ExpenseResource\Schemas;

use App\Models\Expense;
use App\Models\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;

class ExpenseForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->label('Descripción')
                    ->required(),

                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric(),

                Select::make('expense_category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->required()
                    ->preload(),

                Select::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->preload()
                    ->nullable(),

                ...\App\Filament\Forms\Schemas\CurrencyForm::schema(),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
