<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\Currency;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';
    
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Gastos';
    protected static ?string $pluralModelLabel = 'Gastos';
    protected static ?string $navigationLabel = 'Gastos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->rows(1),

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
                    $set('exchange', Currency::find($state)->exchange);
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
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->label('Descripción')->limit(50),
                TextColumn::make('price')->label('Precio')->sortable(),
                TextColumn::make('currency.name')->label('Moneda')->sortable(),
                TextColumn::make('category.name')->label('Categoría')->sortable(),
                TextColumn::make('exchange')->label('Tasa de Cambio')->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
}
