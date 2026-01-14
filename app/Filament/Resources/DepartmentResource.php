<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DepartmentResource\Schemas\DepartmentForm;
use App\Filament\Resources\DepartmentResource\Tables\DepartmentsTable;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationGroup = 'RRHH';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $modelLabel = 'Departamento';
    protected static ?string $pluralModelLabel = 'Departamentos';
    protected static ?string $navigationLabel = 'Departamentos';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('departments.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('departments.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('departments.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('departments.delete');
    }


    public static function form(Form $form): Form
    {
        return DepartmentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
