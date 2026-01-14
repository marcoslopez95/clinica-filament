<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Actions\RefreshTotalCreateAction;
use App\Filament\Actions\RefreshTotalEditAction;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Filament\Actions\RefreshTotalDeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\RefundResource\Schemas\RefundForm;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class RefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'refunds';

    protected static ?string $title = 'Devoluciones';

    private function refundSchema(): array
    {
        $schema = RefundForm::schema();

        return $schema;
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
                    ->money(fn($record) => $record->currency->code ?? 'USD'),

                TextColumn::make('exchange')
                    ->label('Tasa de Cambio'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])

            ->headerActions([
                CreateAction::make()
                    ->label('Nueva Devolución')
                    ->visible(fn (): bool => auth()->user()->can('entries.refunds.create'))
                    ->form($this->refundSchema())
                        ->action(function (array $data, $livewire): void {
                            $invoice = $livewire->getOwnerRecord();

                            $paymentsTotal = $invoice->payments->sum(function ($p) {
                                $exchange = (float) ($p->exchange ?? 1);
                                $amount = (float) ($p->amount ?? 0);
                                $currencyId = (int) ($p->currency_id ?? 0);
                                if ($exchange <= 0) $exchange = 1;
                                return $currencyId === 1 ? $amount : $amount / $exchange;
                            });

                            $refundsTotal = $invoice->refunds()->get()->sum(function ($r) {
                                $exchange = (float) ($r->exchange ?? 1);
                                $amount = (float) ($r->amount ?? 0);
                                $currencyId = (int) ($r->currency_id ?? 0);
                                if ($exchange <= 0) $exchange = 1;
                                return $currencyId === 1 ? $amount : $amount / $exchange;
                            });

                            $newExchange = (float) ($data['exchange'] ?? 1);
                            $newAmountRaw = (float) ($data['amount'] ?? 0);
                            $newCurrencyId = (int) ($data['currency_id'] ?? 0);
                            if ($newExchange <= 0) $newExchange = 1;
                            $newAmount = $newCurrencyId === 1 ? $newAmountRaw : $newAmountRaw / $newExchange;

                            $available = $paymentsTotal - $refundsTotal;

                            if ($newAmount > $available) {
                                Notification::make()
                                    ->body('El monto de las devoluciones no puede superar el total de los pagos realizados')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $invoice->refunds()->create($data);
                            $livewire->dispatch('refreshTotal');
                        }),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('entries.refunds.edit.view'))
                    ->form($this->refundSchema())
                        ->action(function (Model $record, array $data, $livewire): void {
                            if (!auth()->user()->can('entries.refunds.edit')) {
                                Notification::make()
                                    ->title('Acceso denegado')
                                    ->body('No tienes permiso para editar este elemento')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $invoice = $livewire->getOwnerRecord();

                            $paymentsTotal = $invoice->payments->sum(function ($p) {
                                $exchange = (float) ($p->exchange ?? 1);
                                $amount = (float) ($p->amount ?? 0);
                                $currencyId = (int) ($p->currency_id ?? 0);
                                if ($exchange <= 0) $exchange = 1;
                                return $currencyId === 1 ? $amount : $amount / $exchange;
                            });

                            $otherRefunds = $invoice->refunds()
                                ->where('id', '!=', $record->id)
                                ->get()
                                ->sum(function ($r) {
                                    $exchange = (float) ($r->exchange ?? 1);
                                    $amount = (float) ($r->amount ?? 0);
                                    $currencyId = (int) ($r->currency_id ?? 0);
                                    if ($exchange <= 0) $exchange = 1;
                                    return $currencyId === 1 ? $amount : $amount / $exchange;
                                });

                            $newExchange = (float) ($data['exchange'] ?? 1);
                            $newAmountRaw = (float) ($data['amount'] ?? 0);
                            $newCurrencyId = (int) ($data['currency_id'] ?? 0);
                            if ($newExchange <= 0) $newExchange = 1;
                            $newAmount = $newCurrencyId === 1 ? $newAmountRaw : $newAmountRaw / $newExchange;

                            $available = $paymentsTotal - $otherRefunds;

                            if ($newAmount > $available) {
                                Notification::make()
                                    ->body('El monto de las devoluciones no puede superar el total de los pagos realizados')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $record->update($data);
                            $livewire->dispatch('refreshTotal');
                        }),
                RefreshTotalDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    RefreshTotalDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('entries.refunds.view');
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->refundSchema());
    }
}
