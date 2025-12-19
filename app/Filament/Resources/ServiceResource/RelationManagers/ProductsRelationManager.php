<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'productDetails';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('product_id')
                ->label('Producto')
                ->relationship('product', 'name')
                ->preload()
                ->required(),

            TextInput::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Producto'),
                TextColumn::make('quantity')->label('Cantidad'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'name')
                            ->preload()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
