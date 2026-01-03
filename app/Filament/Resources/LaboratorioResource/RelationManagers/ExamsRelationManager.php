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
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Model;

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
            ->pluck('name', 'id');
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
                            ->required(),

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
                    ])
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $exam = Exam::create([
                            'name' => $data['name'],
                            'price' => $data['price'],
                        ]);

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
