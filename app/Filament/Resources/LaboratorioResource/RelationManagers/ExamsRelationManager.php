<?php

namespace App\Filament\Resources\LaboratorioResource\RelationManagers;

use App\Models\Exam;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder as FormPlaceholder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferenceValueResult;
use App\Models\ReferenceValue;
use Filament\Notifications\Notification;
use App\Filament\Resources\ReferenceValueResource\Schemas\ReferenceValueForm;

class ExamsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $modelLabel = 'Examen';
    protected static ?string $pluralModelLabel = 'Examenes';
    protected static ?string $title = 'Examenes de laboratorio';

    protected function getUsedContentIds(Model $owner, ?int $excludeRecordId = null): array
    {
        $query = $owner->details()->where('content_type', Exam::class);
        if ($excludeRecordId) $query->where('id', '!=', $excludeRecordId);
        return $query->pluck('content_id')->toArray();
    }

    protected function getAvailableExams(Model $owner, ?int $excludeRecordId = null)
    {
        $used = $this->getUsedContentIds($owner, $excludeRecordId);
        return Exam::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
            ->whereHas('referenceValues')
            ->pluck('name', 'id');
    }

    protected function fillPriceFromExam($state, $set): void
    {
        $exam = Exam::find($state);
        if ($exam) {
            $set('price', $exam->price ?? 0);
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('content_id')
                ->label('Examen')
                ->options(fn() => $this->getAvailableExams($this->getOwnerRecord()))
                ->searchable()
                ->required()
                ->reactive(),

            TextInput::make('price')
                ->label('Precio')
                ->numeric()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content.name')
                    ->label('Examen')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Precio'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->state(fn(Model $record) => 1 * $record->price),
            ])
            ->headerActions([
                CreateAction::make('add_existing')
                    ->label('Añadir examen existente')
                    ->form([
                        Select::make('content_id')
                            ->label('Examen')
                            ->options(fn() => $this->getAvailableExams($this->getOwnerRecord()))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->fillPriceFromExam($state, $set);
                            }),

                        TextInput::make('price')->label('Precio')->numeric()->required()->default(0),
                    ])
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $owner->details()->create([
                            'content_id' => $data['content_id'],
                            'content_type' => Exam::class,
                            'price' => $data['price'],
                            'quantity' => 1,
                        ]);

                        $livewire->dispatch('refreshTotal');
                    }),

                CreateAction::make('create_exam')
                    ->label('Crear examen')
                    ->modalHeading('Formulario de examen')
                    ->form(\App\Filament\Resources\ExamResource\Schemas\ExamForm::schema())
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();

                        $exam = Exam::create([
                            'name' => $data['name'],
                            'price' => $data['price'],
                            'currency_id' => $data['currency_id'] ?? null,
                        ]);

                        if (!empty($data['referenceValues']) && is_array($data['referenceValues'])) {
                            foreach ($data['referenceValues'] as $rv) {

                                $exam->referenceValues()->create([
                                    'name'      => $rv['name'],
                                    'min_value' => $rv['min_value'],
                                    'max_value' => $rv['max_value'],
                                ]);
                            }
                        }

                        $owner->details()->create([
                            'content_id'   => $exam->id,
                            'content_type' => Exam::class,
                            'price'        => $data['price'],
                            'quantity'     => 0,
                        ]);

                        $livewire->dispatch('refreshTotal');
                    }),
                CreateAction::make('create_reference_value')
                    ->label('Crear valor referencial')
                    ->modalHeading('Crear valor referencial')
                    ->modalWidth('md')
                    ->form([
                        \Filament\Forms\Components\Select::make('exam_id')
                            ->label('Examen')
                            ->options(fn() => Exam::all()->pluck('name', 'id'))
                            ->required(),

                        ...ReferenceValueForm::schema(),
                        ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
                    ])
                    ->action(function (array $data) {
                        ReferenceValue::create($data);
                        Notification::make()
                            ->title('Valor referencial creado')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('load_results')
                    ->label('Cargar resultados')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('lg')
                    ->modalContent(fn (Model $record) => view('filament.actions.manage-exam-results', ['record' => $record])),

                // Action::make('manage_reference_values')
                //     ->label('Valores referenciales')
                //     ->icon('heroicon-o-book-open')
                //     ->modalHeading('Valores referenciales')
                //     ->modalWidth('lg')
                //     ->modalContent(fn (Model $record) => view('filament.actions.manage-reference-values', ['record' => $record])),
                // EditAction::make()
                //     ->action(function (Model $record, array $data, $livewire): void {
                //         $record->update([
                //             'content_id' => $data['content_id'],
                //             'content_type' => Exam::class,
                //             'price' => $data['price'],
                //             'quantity' => 1,
                //         ]);

                //         $livewire->dispatch('refreshTotal');
                //     })
                //     ->after(function ($livewire) { $livewire->dispatch('refreshTotal'); }),
                DeleteAction::make()->after(function ($livewire) { $livewire->dispatch('refreshTotal'); }),
            ])
            ->bulkActions([
                BulkActionGroup::make([ DeleteBulkAction::make() ])
            ]);
    }
}
