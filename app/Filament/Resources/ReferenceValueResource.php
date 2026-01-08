<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferenceValueResource\Pages;
use App\Filament\Resources\ReferenceValueResource\RelationManagers;
use App\Filament\Resources\ReferenceValueResource\Schemas\ReferenceValueForm;
use App\Filament\Resources\ReferenceValueResource\Tables\ReferenceValuesTable;
use App\Models\ReferenceValue;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReferenceValueResource extends Resource
{
    protected static ?string $model = ReferenceValue::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $slug = 'reference-values';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $modelLabel = 'Valor Referencial';
    protected static ?string $pluralModelLabel = 'Valores  Referenciales';
    protected static ?string $navigationLabel = 'Valores  Referenciales';

    public static function form(Form $form): Form
    {
        return ReferenceValueForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ReferenceValuesTable::table($table);
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
            'index' => Pages\ListReferenceValues::route('/'),
            'create' => Pages\CreateReferenceValue::route('/create'),
            'edit' => Pages\EditReferenceValue::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['exam']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'exam.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->exam) {
            $details['Exam'] = $record->exam->name;
        }

        return $details;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

}
