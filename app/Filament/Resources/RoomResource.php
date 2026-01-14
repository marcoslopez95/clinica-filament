<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\Schemas\RoomForm;
use App\Filament\Resources\RoomResource\Tables\RoomsTable;
use App\Models\Room;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $slug = 'rooms';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Habitación';
    protected static ?string $pluralModelLabel = 'Habitaciones';
    protected static ?string $navigationLabel = 'Habitaciones';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('rooms.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('rooms.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('rooms.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('rooms.delete');
    }

    public static function form(Form $form): Form
    {
        return RoomForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return RoomsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
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
