<?php

namespace App\Filament\Resources\Security;

use App\Filament\Resources\Security\PermissionCategoryResource\Pages;
use App\Filament\Resources\Security\PermissionCategoryResource\RelationManagers;
use App\Models\PermissionCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionCategoryResource extends Resource
{
    protected static ?string $model = PermissionCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Seguridad';

    protected static ?string $modelLabel = 'Categoría de Permiso';

    protected static ?string $pluralModelLabel = 'Categorías de Permisos';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('categories.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('categories.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('categories.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('categories.delete');
    }

    public static function canBulkDelete(): bool
    {
        return auth()->user()->can('categories.bulk_delete');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPermissionCategories::route('/'),
            'create' => Pages\CreatePermissionCategory::route('/create'),
            'edit' => Pages\EditPermissionCategory::route('/{record}/edit'),
        ];
    }
}
