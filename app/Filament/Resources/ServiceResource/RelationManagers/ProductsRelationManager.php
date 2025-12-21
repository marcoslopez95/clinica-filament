<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder as FormPlaceholder;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'productDetails';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('product_id')
                ->label('Producto')
                ->options(fn () => Product::pluck('name', 'id')->toArray())
                ->searchable()
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
                TextColumn::make('product.name')
                    ->label('Producto asociado')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Cantidad asignada'),
            ])
                ->headerActions([
                    CreateAction::make()->label('Crear ' . static::$modelLabel)
                        ->form([
                        Select::make('product_id')
                            ->label('Producto')
                            ->options(function () {
                                $owner = $this->getOwnerRecord();
                                $used = $owner->productDetails()->pluck('product_id')->toArray();
                                return Product::whereNotIn('id', $used)->pluck('name', 'id')->toArray();
                            })
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ]),
            ])
            ->actions([
                EditAction::make()->form([
                    Hidden::make('product_id')
                        ->default(fn ($record) => $record->product_id)
                        ->required(),

                    FormPlaceholder::make('product_label')
                        ->label('Producto')
                        ->content(fn ($record) => $record->product?->name ?? '-'),

                    TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->required(),
                ]),
                DeleteAction::make(),
            ])->emptyStateDescription('');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Productos del servicio';
    }
}
