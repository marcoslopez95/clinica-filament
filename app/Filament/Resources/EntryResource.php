<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\EntryResource\Pages;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\TypeDocument;
use App\Enums\InvoiceType;
use Filament\Forms\Components\Hidden;
use App\Models\Product;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\EntryResource\RelationManagers\ProductsRelationManager;

class EntryResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'entries';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Entrada';
    protected static ?string $pluralModelLabel = 'Entradas';
    protected static ?string $navigationLabel = 'Entradas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('invoiceable_id')
                    ->label('Proveedor')
                    ->options(fn() => Supplier::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $supplier = Supplier::findOrFail($state);
                        $set('full_name', $supplier->name);
                        $set('dni', $supplier->rif);
                    }),

                TextInput::make('invoice_number')
                    ->label('Número de factura')
                    ->columnSpan(2),

                TextInput::make('full_name')
                    ->label('Nombre'),

                TextInput::make('dni')
                    ->label('Documento'),

                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->options(fn() => TypeDocument::all()->pluck('name','id'))
                    ->default(fn() => TypeDocument::where('code', 'RIF')->first()?->id)
                    ->required(),

                DatePicker::make('date')
                    ->label('Fecha de factura')->default(now()->format('Y-m-d')),

                Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?int $state) {
                        $currency = Currency::find($state);
                        $set('exchange', $currency->exchange ?? null);
                    }),

                TextInput::make('exchange')
                    ->label('Tasa de cambio')
                    ->numeric()
                    ->required(),

                TextInput::make('total')
                    ->label('Monto')
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->dehydrated()
                    ->suffixAction(
                        Action::make('calculateTotal')
                            ->icon('heroicon-m-calculator')
                            ->label('Calcular')
                            ->action(function (Set $set, ?Invoice $record) {
                                if ($record) {
                                    $total = $record->details()->sum('subtotal');
                                    $record->update(['total' => $total]);
                                    $set('total', $total);
                                }
                            })
                    ),

                DatePicker::make('credit_date')
                    ->label('Fecha de crédito')
                    ->nullable(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Invoice $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Invoice $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->sortable()->searchable(),
                TextColumn::make('dni')->sortable()->searchable(),
                TextColumn::make('date')->label('Fecha')->date()->sortable()->searchable(),
                TextColumn::make('total')->label('Monto'),
                TextColumn::make('currency.name')->label('Moneda'),
                TextColumn::make('exchange')->label('Tasa de cambio'),
                TextColumn::make('to_pay')->label('Por Pagar'),
                TextColumn::make('status')->label('Estado')->searchable()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('Status')
                ->options(collect(InvoiceStatus::cases())
                    ->map(fn(InvoiceStatus $status) => $status->value)
                    ->toArray()
                )->attribute('status')
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntries::route('/'),
            'create' => Pages\CreateEntry::route('/create'),
            'edit' => Pages\EditEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('invoice_type', InvoiceType::INVENTORY->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
