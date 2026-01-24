<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Filament\Resources\ExamResource\Schemas\ExamForm;
use App\Filament\Resources\ExamResource\Tables\ExamsTable;
use App\Models\Exam;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $slug = 'exams';
    protected static ?string $navigationGroup = 'Laboratorio';
    protected static ?string $modelLabel = 'Examen';
    protected static ?string $pluralModelLabel = 'Examenes';
    protected static ?string $navigationLabel = 'Examenes';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('exams.list') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('exams.create') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('exams.edit') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('exams.delete') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(ExamForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ExamsTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReferenceValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [];
    }
}
