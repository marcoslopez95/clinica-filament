<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms\Form;
use App\Filament\Resources\ReferenceValueResource\Schemas\ReferenceValueForm;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ReferenceValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'referenceValues';

    protected static ?string $title = 'Valores referencial';
    protected static ?string $modelLabel = 'Valor referencial';
    protected static ?string $pluralModelLabel = 'Valores referenciales';

    public function form(Form $form): Form
    {
        return $form->schema(ReferenceValueForm::schema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),

                TextColumn::make('min_value')
                    ->label('Mínimo'),

                TextColumn::make('max_value')
                    ->label('Máximo'),

                TextColumn::make('unit.name')
                    ->label('Unidad'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => auth()->user()->can('exams.reference_values.create')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('exams.reference_values.edit.view'))
                    ->action(function (Model $record, array $data): void {
                        if (!auth()->user()->can('exams.reference_values.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar este elemento')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update($data);
                    }),
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()->can('exams.reference_values.delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->can('exams.reference_values.bulk_delete')),
                ]),
            ]);
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('exams.reference_values.view');
    }
}