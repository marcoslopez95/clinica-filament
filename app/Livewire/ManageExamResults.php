<?php

namespace App\Livewire;

use App\Models\InvoiceDetail;
use App\Models\ReferenceValueResult;
use App\Models\ReferenceValue;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
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

class ManageExamResults extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public InvoiceDetail $invoiceDetail;

    public function mount(InvoiceDetail $record): void
    {
        $this->invoiceDetail = $record;
    }

    private function formSchema(): array
    {
        $exam = $this->invoiceDetail->content;
        $options = $exam ? $exam->referenceValues()->pluck('name', 'id') : [];

        return [
            Select::make('reference_value_id')
                ->label('Valor referencial')
                ->options(fn() => $options)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $rv = ReferenceValue::find($state);
                    $set('min_value', $rv->min_value ?? null);
                    $set('max_value', $rv->max_value ?? null);
                }),

            ...\App\Filament\Resources\ReferenceValueResource\Schemas\ReferenceValueForm::schema(),

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

                TextColumn::make('result')
                    ->label('Resultado'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir Resultado')
                    ->modalHeading('Crear Resultado')
                    ->modalWidth('md')
                    ->form($this->formSchema())
                    ->action(function (array $data) {
                        $exists = ReferenceValueResult::where('invoice_detail_id', $this->invoiceDetail->id)
                            ->where('reference_value_id', $data['reference_value_id'])
                            ->exists();

                        if ($exists) {
                            Notification::make()
                                ->title('Error')
                                ->body('Ya existe un resultado para ese valor referencial')
                                ->danger()
                                ->send();
                            return;
                        }

                        ReferenceValueResult::create(array_merge($data, ['invoice_detail_id' => $this->invoiceDetail->id]));
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Editar Resultado')
                    ->modalWidth('md')
                    ->form($this->formSchema())
                    ->action(function (ReferenceValueResult $record, array $data) {
                        $record->update($data);
                    }),

                DeleteAction::make(),
            ]);
    }

    public function render()
    {
        return view('livewire.manage-exam-results');
    }
}
