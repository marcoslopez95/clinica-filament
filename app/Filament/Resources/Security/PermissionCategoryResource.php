<?php

namespace App\Filament\Resources\Security;

use App\Filament\Resources\Security\PermissionCategoryResource\Pages;
use App\Models\PermissionCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->action(function (PermissionCategory $record, array $data) {
                        if (!auth()->user()->can('categories.update')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para actualizar categorías')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update($data);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('categories.bulk_delete')),
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
