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

class ReferenceValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'referenceValues';

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
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
