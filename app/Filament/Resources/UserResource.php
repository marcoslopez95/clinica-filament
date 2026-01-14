<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Schemas\UserForm;
use App\Filament\Resources\UserResource\Tables\UsersTable;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static ?string $navigationGroup = 'Administración';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('users.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('users.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('users.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('users.delete');
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return UserForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'name'];
    }
}
