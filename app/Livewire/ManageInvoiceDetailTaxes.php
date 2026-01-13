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
use App\Filament\Actions\RefreshTotalDeleteAction;
use Livewire\Component;
use Filament\Notifications\Notification;
use App\Filament\Resources\InvoiceDetailTaxResource\Schemas\InvoiceDetailTaxForm;

class ManageInvoiceDetailTaxes extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public InvoiceDetail $invoiceDetail;

    public function mount(InvoiceDetail $record): void
    {
        $this->invoiceDetail = $record;
    }

    private function Schema(): array
    {
        $schema = InvoiceDetailTaxForm::schema();
        $that = $this;

        foreach ($schema as $component) {
            $name = method_exists($component, 'getName') ? $component->getName() : (method_exists($component, 'getStatePath') ? $component->getStatePath() : null);

            if ($name === 'percentage') {
                $component->afterStateUpdated(function ($state, $set) use ($that) {
                    $subtotal = (float) ($that->invoiceDetail->price ?? 0) * (float) ($that->invoiceDetail->quantity ?? 0);
                    $percentage = (float) $state;
                    $amount = $subtotal * ($percentage / 100);
                    $set('amount', round($amount, 2));
                });
            }

            if ($name === 'amount') {
                $component->afterStateUpdated(function ($state, $set) use ($that) {
                    $subtotal = (float) ($that->invoiceDetail->price ?? 0) * (float) ($that->invoiceDetail->quantity ?? 0);
                    $amount = (float) $state;
                    if ($subtotal > 0) {
                        $set('percentage', round(($amount / $subtotal) * 100, 2));
                    }
                });
            }
        }

        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(InvoiceDetailTax::query()
            ->where('invoice_detail_id', $this->invoiceDetail->id))
            ->columns(\App\Filament\Resources\InvoiceDetailTaxResource\Tables\InvoiceDetailTaxesTable::columns())
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir Impuesto')
                    ->modalHeading('Crear Impuesto')
                    ->modalWidth('md')
                    ->form($this->Schema())
                    ->action(function (array $data) {
                        $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                        $currentTaxesSum = $this->invoiceDetail->taxes()->sum('amount');
                        $newTotalTaxes = $currentTaxesSum + (float) $data['amount'];

                        if ($newTotalTaxes > $subtotal) {
                            Notification::make()
                                ->title('Error de validación')
                                ->body('
                                    La suma de los impuestos no puede superar el subtotal
                                     del producto ($' . number_format($subtotal, 2) . ')'
                                    )
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
                    ->form($this->Schema())
                    ->action(function (InvoiceDetailTax $record, array $data) {
                        $subtotal = (float) ($this->invoiceDetail->price ?? 0) * (float) ($this->invoiceDetail->quantity ?? 0);
                        $currentTaxesSum = $this->invoiceDetail->taxes()
                            ->where('id', '!=', $record->id)
                            ->sum('amount');

                        $newTotalTaxes = $currentTaxesSum + (float) $data['amount'];

                        if ($newTotalTaxes > $subtotal) {
                            Notification::make()
                                ->title('Error de validación')
                                ->body(
                                    'La suma de los impuestos no puede superar el 
                                    subtotal del producto ($' . number_format($subtotal, 2) . ')'
                                    )
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update($data);

                        $this->dispatch('refreshTotal');
                    }),
                    
                    RefreshTotalDeleteAction::make(),
            ]);
    }

    public function render()
    {
        return view('livewire.manage-invoice-detail-taxes');
    }
}
