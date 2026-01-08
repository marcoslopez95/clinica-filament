<?php

namespace App\Livewire;

use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailTax;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

class ManageInvoiceDetailTaxes extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public InvoiceDetail $invoiceDetail;

    public function mount(InvoiceDetail $record): void
    {
        $this->invoiceDetail = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(InvoiceDetailTax::query()->where('invoice_detail_id', $this->invoiceDetail->id))
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('percentage')
                    ->label('Porcentaje')
                    ->suffix('%'),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir Impuesto')
                    ->modalHeading('Crear Impuesto')
                    ->modalWidth('md')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        \Filament\Forms\Components\TextInput::make('percentage')
                            ->label('Porcentaje')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->suffix('%')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                                $percentage = (float) $state;
                                $amount = $subtotal * ($percentage / 100);
                                $set('amount', round($amount, 2));
                            }),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                                $amount = (float) $state;
                                if ($subtotal > 0) {
                                    $set('percentage', round(($amount / $subtotal) * 100, 2));
                                }
                            }),
                    ])
                    ->action(function (array $data) {
                        $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                        $currentTaxesSum = $this->invoiceDetail->taxes()->sum('amount');
                        $newTotalTaxes = $currentTaxesSum + (float) $data['amount'];

                        if ($newTotalTaxes > $subtotal) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error de validación')
                                ->body('La suma de los impuestos no puede superar el subtotal del producto ($' . number_format($subtotal, 2) . ')')
                                ->danger()
                                ->send();

                            return;
                        }

                        $data['invoice_detail_id'] = $this->invoiceDetail->id;
                        InvoiceDetailTax::create($data);

                        $this->dispatch('refreshTotal');
                    })
                    ->after(null),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Editar Impuesto')
                    ->modalWidth('md')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        \Filament\Forms\Components\TextInput::make('percentage')
                            ->label('Porcentaje')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->suffix('%')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                                $percentage = (float) $state;
                                $amount = $subtotal * ($percentage / 100);
                                $set('amount', round($amount, 2));
                            }),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                                $amount = (float) $state;
                                if ($subtotal > 0) {
                                    $set('percentage', round(($amount / $subtotal) * 100, 2));
                                }
                            }),
                    ])
                    ->action(function (InvoiceDetailTax $record, array $data) {
                        $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                        $currentTaxesSum = $this->invoiceDetail->taxes()
                            ->where('id', '!=', $record->id)
                            ->sum('amount');

                        $newTotalTaxes = $currentTaxesSum + (float) $data['amount'];

                        if ($newTotalTaxes > $subtotal) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error de validación')
                                ->body('La suma de los impuestos no puede superar el subtotal del producto ($' . number_format($subtotal, 2) . ')')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update($data);

                        $this->dispatch('refreshTotal');
                    })
                    ->after(null),
                DeleteAction::make()
                    ->after(fn() => $this->dispatch('refreshTotal')),
            ]);
    }

    public function render()
    {
        return view('livewire.manage-invoice-detail-taxes');
    }
}
