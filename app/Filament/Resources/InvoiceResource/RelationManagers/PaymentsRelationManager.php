<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Filament\Forms\Components\Invoiceable\ToPayInvoiceable;
use App\Models\Currency;
use App\Services\Helper;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Pagos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ToPayInvoiceable::make()
                ->dehydrated(false),
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->label('Método de Pago')
                    ->required()
                    ->live(),

                Select::make('currency_id')
                    ->relationship(
                        'currency',
                        'name',
                        modifyQueryUsing: fn(Get $get, Builder $query) => $query
                            ->whereRelation(
                                'paymentMethods',
                                'payment_methods.id',
                                $get('payment_method_id')
                            )
                    )
                    ->label('Moneda')
                    ->disabled(fn(Get $get) => !$get('payment_method_id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, mixed $state,RelationManager $livewire) {
                        $currency = Currency::find($state);
                        $set('exchange', $currency->exchange ?? 0);
                        if ($currency) {
                            $set('per_pay_invoiceable', ToPayInvoiceable::recalculateBalance($currency, $livewire));
                        }
                    }),

                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->required()
                    ->disabled(fn(Get $get) => !$get('currency_id'))
                    ->live(debounce: 500),

                TextInput::make('exchange')
                    ->label('Tasa de Cambio')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago'),

                TextColumn::make('currency.name')
                    ->label('Moneda'),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->state(fn($record) => Helper::formatCurrency($record->amount, $record->currency)),

                TextColumn::make('exchange')
                    ->label('Tasa de Cambio'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn (): bool => auth()->user()->can('invoices.payments.create'))
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('invoices.payments.edit.view'))
                    ->action(function (Model $record, array $data, $livewire): void {
                        if (!auth()->user()->can('invoices.payments.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar este elemento')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update($data);
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('invoices.payments.view');
    }
}
