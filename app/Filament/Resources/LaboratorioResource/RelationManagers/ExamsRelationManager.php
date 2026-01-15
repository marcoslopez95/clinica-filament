<?php

namespace App\Filament\Resources\LaboratorioResource\RelationManagers;

use App\Models\Exam;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferenceValue;
use Filament\Notifications\Notification;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Filament\Actions\LoadResultsAction;

use App\Enums\UnitCategoryEnum;
use Illuminate\Database\Eloquent\Builder;

class ExamsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $modelLabel = 'Examen';
    protected static ?string $pluralModelLabel = 'Examenes';
    protected static ?string $title = 'Examenes de laboratorio';

    public static function schema($owner): array
    {
        return [
            Select::make('content_id')
                ->label('Examen')
                ->options(function () use ($owner) {
                    $used = $owner->details()
                        ->where('content_type', Exam::class)
                        ->pluck('content_id')
                        ->toArray();

                    return Exam::query()
                        ->when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $exam = Exam::find($state);
                    if ($exam) {
                        $set('price', $exam->price ?? 0);
                    }
                }),

            TextInput::make('price')
                ->label('Precio')
                ->default(0),
        ];
    }


    public static function configure(Form $form, $owner): Form
    {
        return $form->schema([
            ...self::schema($owner),
            \App\Filament\Forms\Schemas\TimestampForm::schema(),
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
                    ->visible(fn (): bool => auth()->user()->can('laboratories.details.attach'))
                    ->modalHeading(false)
                    ->form(fn() => self::schema($this->getOwnerRecord()))
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $detail = $owner->details()->create([
                            'content_id' => $data['content_id'],
                            'content_type' => Exam::class,
                            'price' => $data['price'],
                            'quantity' => 1,
                        ]);

                        $exam = Exam::find($data['content_id']);
                        if ($exam) {
                            foreach ($exam->referenceValues as $rv) {
                                $detail->referenceResults()->create([
                                    'reference_value_id' => $rv->id,
                                    'result' => null,
                                ]);
                            }
                        }

                        $livewire->dispatch('refreshTotal');
                    }),

                CreateAction::make('create_exam')
                    ->label('Crear examen')
                    ->visible(fn (): bool => auth()->user()->can('laboratories.details.create'))
                    ->modalHeading(false)
                    ->form(function() {
                        return [
                            ... \App\Filament\Resources\ExamResource\Schemas\ExamForm::schema(),
                            \Filament\Forms\Components\Repeater::make('referenceValues')
                                ->label('Valores Referenciales')
                                ->schema(ReferenceValueForm::schema())
                                ->columns(4)
                                ->default([])
                        ];
                    })
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
                                    'unit_id'   => $rv['unit_id'] ?? null,
                                ]);
                            }
                        }

                        $detail = $owner->details()->create([
                            'content_id'   => $exam->id,
                            'content_type' => Exam::class,
                            'price'        => $data['price'],
                            'quantity'     => 1,
                        ]);

                        foreach ($exam->referenceValues as $rv) {
                            $detail->referenceResults()->create([
                                'reference_value_id' => $rv->id,
                                'result' => null,
                            ]);
                        }

                        $livewire->dispatch('refreshTotal');
                    }),
                CreateAction::make('create_reference_value')
                    ->label('Crear valor referencial')
                    ->modalHeading(false)
                    ->modalWidth('md')
                    ->form([

                        Select::make('exam_id')
                            ->label('Examen')
                            ->options(fn() => Exam::all()->pluck('name', 'id'))
                            ->required(),

                        Select::make('unit_id')
                            ->label('Unidad')
                            ->relationship(
                                name: 'unit',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('categories', function (Builder $query) {
                                    $query->where('name', UnitCategoryEnum::LABORATORY->value);
                                })
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Nombre')
                            ->unique(column: 'name', ignoreRecord: true, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get) {
                                return $rule
                                    ->where('exam_id', $get('exam_id'))
                                    ->whereNull('deleted_at');
                            })
                            ->required(),

                        TextInput::make('min_value')
                            ->label('Mínimo')
                            ->numeric(),

                        TextInput::make('max_value')
                            ->label('Máximo')
                            ->numeric(),
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
                LoadResultsAction::make(),

                RefreshTotalDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ])
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('laboratories.details.view');
    }
}
