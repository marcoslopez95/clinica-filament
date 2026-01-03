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

                Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name')
                    ->required()
                    ->reactive()
                    ->preload()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('exchange', Currency::find($state)?->exchange ?? 0);
                    }),

                Select::make('expense_category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->required()
                    ->preload(),

                TextInput::make('exchange')
                    ->label('Tasa de Cambio')
                    ->readOnly()
                    ->numeric()
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Expense $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha Última Modificación')
                    ->content(fn(?Expense $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
