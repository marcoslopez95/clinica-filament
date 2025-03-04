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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Apellido')
                    ->required(),

                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->relationship('typeDocument', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('dni')
                    ->label('DNI')
                    ->required(),

                DatePicker::make('born_date')
                ->label('Fecha de Nacimiento'),

                TextInput::make('cost')->label('Costo')
                ->required()
                ->numeric(),


                Select::make('specialization_id')
                    ->relationship('specialization', 'name')
                    ->searchable()
                    ->label('Especialización'),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Doctor $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Doctor $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                ->label('Nombre'),

                TextColumn::make('last_name')
                ->label('Apellido'),

                TextColumn::make('typeDocument.name')
                    ->label('Tipo de Documento')
                    ->sortable(),

                TextColumn::make('dni')
                ->label('Num. Documento'),

                TextColumn::make('born_date')
                    ->label('Fecha de Nacimiento')
                    ->date(),

                TextColumn::make('cost')->label('Costo'),

                TextColumn::make('specialization.name')->label('Especialización'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
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
