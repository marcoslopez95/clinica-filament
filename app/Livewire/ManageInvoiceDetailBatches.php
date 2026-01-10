<?php

namespace App\Livewire;

use App\Models\InvoiceDetail;
use App\Models\ProductBatchDetail;
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
use Filament\Notifications\Notification;
use App\Filament\Resources\ProductBatchDetailResource\Schemas\ProductBatchDetailForm;
use App\Filament\Resources\ProductBatchDetailResource\Tables\ProductBatchDetailsColumns;

class ManageInvoiceDetailBatches extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public InvoiceDetail $invoiceDetail;

    public function mount(InvoiceDetail $record): void
    {
        $this->invoiceDetail = $record;
    }

    private function schema(): array
    {
        return ProductBatchDetailForm::schema();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductBatchDetail::query()->where('invoice_detail_id', $this->invoiceDetail->id))
            ->columns(ProductBatchDetailsColumns::columns())
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir Lote')
                    ->modalHeading('Crear Lote')
                    ->modalWidth('md')
                    ->form($this->schema())
                    ->action(function (array $data) {
                        $currentSum = $this->invoiceDetail->batchDetails()->sum('quantity');
                        $newSum = $currentSum + (float) ($data['quantity'] ?? 0);
                        $max = (float) ($this->invoiceDetail->quantity ?? 0);

                        if ($newSum > $max) {
                            Notification::make()
                                ->title('Error de validación')
                                ->body('La suma de las cantidades de los lotes no puede superar la cantidad del producto (' . $max . ')')
                                ->danger()
                                ->send();
                            return;
                        }

                        $data['invoice_detail_id'] = $this->invoiceDetail->id;
                        ProductBatchDetail::create($data);
                        $this->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Editar Lote')
                    ->modalWidth('md')
                    ->form($this->schema())
                    ->action(function (ProductBatchDetail $record, array $data) {
                        $currentSum = $this->invoiceDetail->batchDetails()
                            ->where('id', '!=', $record->id)
                            ->sum('quantity');

                        $newSum = $currentSum + (float) ($data['quantity'] ?? 0);
                        $max = (float) ($this->invoiceDetail->quantity ?? 0);

                        if ($newSum > $max) {
                            Notification::make()
                                ->title('Error de validación')
                                ->body('La suma de las cantidades de los lotes no puede superar la cantidad del producto (' . $max . ')')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update($data);
                        $this->dispatch('refreshTotal');
                    }),

                DeleteAction::make()
                    ->after(fn() => $this->dispatch('refreshTotal')),
            ]);
    }

    public function render()
    {
        return view('livewire.manage-invoice-detail-batches');
    }
}
