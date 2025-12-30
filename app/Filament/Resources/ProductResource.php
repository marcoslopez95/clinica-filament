<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\ProductResource\RelationManagers\InventoryRelationManager;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'products';

    protected static ?string $navigationGroup = 'Almacén';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $navigationLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('buy_price')
                    ->label('Precio de Compra')
                    ->required()
                    ->numeric(),

                TextInput::make('sell_price')
                    ->label('Precio de Venta')
                    ->required()
                    ->numeric(),

                Select::make('unit_id')
                    ->label('Unidad')
                    ->required()
                    ->relationship('unit', 'name')
                    ->preload(),

                Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->preload(),

                Select::make('product_category_id')
                    ->label('Categoría')
                    ->required()
                    ->relationship('productCategory', 'name')
                    ->preload(),

                Select::make('currency_id')
                    ->label('Moneda')
                    ->required()
                    ->relationship('currency', 'name')
                    ->preload(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Product $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha Última Modificación')
                    ->content(fn(?Product $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('buy_price')
                ->label('Precio de Compra'),

                TextColumn::make('sell_price')
                ->label('Precio de Venta'),

                TextColumn::make('unit.name')
                    ->label('Unidad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('productCategory.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.name')
                    ->label('Moneda')
                    ->searchable()
                    ->sortable(),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            InventoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['unit']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'unit.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->unit) {
            $details['Unit'] = $record->unit->name;
        }

        return $details;
    }
}
