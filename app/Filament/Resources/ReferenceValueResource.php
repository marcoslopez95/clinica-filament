<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferenceValueResource\Pages;
use App\Filament\Resources\ReferenceValueResource\RelationManagers;
use App\Models\ReferenceValue;
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
        return $form
            ->schema([
                Select::make('exam_id')
                    ->label('Exam')
                    ->relationship('exam', 'name')
                    ->required()
                    ->preload(),

                TextInput::make('name')
                    ->label('Name')
                    ->required(),

                TextInput::make('min_value')
                    ->label('Min Value')
                    ->required()
                    ->numeric(),

                TextInput::make('max_value')
                    ->label('Max Value')
                    ->required()
                    ->numeric(),

                Placeholder::make('created_at')
                    ->label('Created At')
                    ->content(fn(?ReferenceValue $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Updated At')
                    ->content(fn(?ReferenceValue $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exam.name')
                    ->label('Exam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('min_value')
                    ->label('Min Value')
                    ->sortable(),

                TextColumn::make('max_value')
                    ->label('Max Value')
                    ->sortable(),
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
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
}
