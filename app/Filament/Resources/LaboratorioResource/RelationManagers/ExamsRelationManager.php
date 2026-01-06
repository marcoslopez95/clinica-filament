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
                TextColumn::make('content.name')->label('Examen')->searchable()->sortable(),
                TextColumn::make('price')->label('Precio'),
                TextColumn::make('subtotal')->label('Subtotal')->state(fn(Model $record) => 1 * $record->price),
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
                    ->form([
                        TextInput::make('name')->label('Nombre')->required(),
                        TextInput::make('price')->label('Precio')->numeric()->required()->default(0),
                        Repeater::make('reference_values')
                            ->label('Valores referenciales')
                            ->schema([
                                TextInput::make('name')->label('Nombre')->required(),
                                TextInput::make('min_value')->label('Valor Mínimo')->numeric()->required(),
                                TextInput::make('max_value')->label('Valor Máximo')->numeric()->required(),
                            ])
                                ->addActionLabel('Añadir valor referencial')
                            ->columns(1)
                            ->collapsed(false),
                    ])
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $exam = Exam::create([
                            'name' => $data['name'],
                            'price' => $data['price'],
                        ]);

                        if (!empty($data['reference_values']) && is_array($data['reference_values'])) {
                            foreach ($data['reference_values'] as $rv) {
                                ReferenceValue::create([
                                    'exam_id' => $exam->id,
                                    'name' => $rv['name'] ?? null,
                                    'min_value' => $rv['min_value'] ?? null,
                                    'max_value' => $rv['max_value'] ?? null,
                                ]);
                            }
                        }

                        $owner->details()->create([
                            'content_id' => $exam->id,
                            'content_type' => Exam::class,
                            'price' => $data['price'],
                            'quantity' => 1,
                        ]);

                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                Action::make('load_results')
                    ->label('Cargar resultados')
                    ->icon('heroicon-o-document-text')
                    ->form(function (Model $record) {
                        $exam = $record->content;

                        $initial = [];
                        $existingResults = ReferenceValueResult::where('invoice_detail_id', $record->id)->get();
                        foreach ($existingResults as $er) {
                            $initial[] = [
                                'reference_value_id' => $er->reference_value_id,
                                'result' => $er->result,
                            ];
                        }

                        $options = [];
                        if ($exam) {
                            $options = $exam->referenceValues()->pluck('name', 'id')->toArray();
                        }

                        return [
                            Repeater::make('results')
                                ->label('Resultados')
                                ->schema([
                                    Select::make('reference_value_id')
                                        ->label('Valor referencial')
                                        ->options(fn() => $options)
                                        ->reactive()
                                        ->required(),

                                    FormPlaceholder::make('min')
                                        ->label('Mínimo')
                                        ->content(fn($get) => ($ref = ReferenceValue::find($get('reference_value_id'))) ? $ref->min_value : ''),

                                    FormPlaceholder::make('max')
                                        ->label('Máximo')
                                        ->content(fn($get) => ($ref = ReferenceValue::find($get('reference_value_id'))) ? $ref->max_value : ''),

                                    TextInput::make('result')->label('Resultado')->required(false),
                                ])
                                ->default($initial)
                                   ->addActionLabel('Añadir resultado')
                                ->columns(1)
                                ->collapsed(false),
                        ];
                    })
                    ->action(function (Model $record, array $data, $livewire) {
                        $exam = $record->content;
                        if (! $exam) return;

                        $items = $data['results'] ?? [];
                        foreach ($items as $item) {
                            $refId = $item['reference_value_id'] ?? null;
                            $value = $item['result'] ?? null;
                            if (! $refId) continue;

                            ReferenceValueResult::updateOrCreate(
                                [
                                    'invoice_detail_id' => $record->id,
                                    'reference_value_id' => $refId,
                                ],
                                ['result' => $value]
                            );
                        }

                        Notification::make()
                            ->success()
                            ->title('Resultados guardados')
                            ->send();
                    })
                    ->requiresConfirmation(false)
                    ->modalWidth('lg'),
                EditAction::make()
                    ->form(function (Form $form) {
                        return $form->schema([
                            Select::make('content_id')
                                ->label('Examen')
                                ->options(function (Model $record) {
                                    return $this->getAvailableExams($this->getOwnerRecord(), $record->id ?? null);
                                })
                                ->searchable()
                                ->required(),

                            TextInput::make('price')
                                ->label('Precio')
                                ->numeric()
                                ->required(),
                        ]);
                    })
                    ->action(function (Model $record, array $data, $livewire): void {
                        $record->update([
                            'content_id' => $data['content_id'],
                            'content_type' => Exam::class,
                            'price' => $data['price'],
                            'quantity' => 1,
                        ]);

                        $livewire->dispatch('refreshTotal');
                    })
                    ->after(function ($livewire) { $livewire->dispatch('refreshTotal'); }),
                DeleteAction::make()->after(function ($livewire) { $livewire->dispatch('refreshTotal'); }),
            ])
            ->bulkActions([
                BulkActionGroup::make([ DeleteBulkAction::make() ])
            ]);
    }
}
