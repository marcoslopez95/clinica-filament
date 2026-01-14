<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\Schemas\PatientForm;
use App\Filament\Resources\PatientResource\Tables\PatientsTable;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $slug = 'patients';

    protected static ?string $navigationGroup = 'RRHH';
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $modelLabel = 'Paciente';
    protected static ?string $pluralModelLabel = 'Pacientes';
    protected static ?string $navigationLabel = 'Pacientes';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('patients.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('patients.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('patients.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('patients.delete');
    }

    public static function form(Form $form): Form
    {
        return PatientForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['typeDocument']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['typeDocument.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->typeDocument) {
            $details['TypeDocument'] = $record->typeDocument->name;
        }

        return $details;
    }
}
