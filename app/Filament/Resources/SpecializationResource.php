<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecializationResource\Pages;
use App\Filament\Resources\SpecializationResource\Schemas\SpecializationForm;
use App\Filament\Resources\SpecializationResource\Tables\SpecializationsTable;
use App\Models\Specialization;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpecializationResource extends Resource
{
    protected static ?string $model = Specialization::class;

    protected static ?string $slug = 'specializations';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $modelLabel = 'Especialización';
    protected static ?string $pluralModelLabel = 'Especializaciones';
    protected static ?string $navigationLabel = 'Especializaciones';

    public static function form(Form $form): Form
    {
        return SpecializationForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SpecializationsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecializations::route('/'),
            'create' => Pages\CreateSpecialization::route('/create'),
            'edit' => Pages\EditSpecialization::route('/{record}/edit'),
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
