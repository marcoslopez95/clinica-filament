<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DoctorResource\Schemas\DoctorForm;
use App\Filament\Resources\DoctorResource\Tables\DoctorsTable;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $slug = 'doctors';

    protected static ?string $navigationGroup = 'RRHH';
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $modelLabel = 'Doctor';
    protected static ?string $pluralModelLabel = 'Doctores';
    protected static ?string $navigationLabel = 'Doctores';

    public static function form(Form $form): Form
    {
        return DoctorForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return DoctorsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
