<?php

namespace App\Livewire;

use App\Models\InvoiceDetail;
use App\Models\ReferenceValueResult;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Notifications\Notification;
use App\Models\Exam;

class ManageExamResults extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public InvoiceDetail $invoiceDetail;

    public function mount(InvoiceDetail $record): void
    {
        $this->invoiceDetail = $record;

        if ($this->invoiceDetail->content_type === Exam::class && $this->invoiceDetail->content) {
            $exam = $this->invoiceDetail->content;
            $existingRefValueIds = $this->invoiceDetail->referenceResults()->pluck('reference_value_id')->toArray();

            foreach ($exam->referenceValues as $rv) {
                if (!in_array($rv->id, $existingRefValueIds)) {
                    $this->invoiceDetail->referenceResults()->create([
                        'reference_value_id' => $rv->id,
                        'result' => null,
                    ]);
                }
            }
        }
    }

    private function formSchema(): array
    {
        return [
            TextInput::make('reference_value_name')
                ->label('Valor referencial')
                ->formatStateUsing(fn ($record) => $record->referenceValue->name ?? '')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('min_value')
                ->label('Mínimo')
                ->formatStateUsing(fn ($record) => $record->referenceValue->min_value ?? '')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('max_value')
                ->label('Máximo')
                ->formatStateUsing(fn ($record) => $record->referenceValue->max_value ?? '')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('unit_name')
                ->label('Unidad')
                ->formatStateUsing(fn ($record) => $record->referenceValue->unit->name ?? '')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('result')
                ->label('Resultado')
                ->required(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ReferenceValueResult::query()->where('invoice_detail_id', $this->invoiceDetail->id))
            ->columns([
                TextColumn::make('referenceValue.name')
                    ->label('Valor referencial'),

                TextColumn::make('referenceValue.unit.name')
                    ->label('Unidad'),

                TextColumn::make('result')
                    ->label('Resultado'),
            ])
            ->headerActions([
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('reference_value_results.edit.view'))
                    ->modalHeading('Editar Resultado')
                    ->modalWidth('md')
                    ->form($this->formSchema())
                    ->action(function (ReferenceValueResult $record, array $data) {
                        if (!auth()->user()->can('reference_value_results.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar resultados de exámenes')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update($data);
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.manage-exam-results');
    }
}