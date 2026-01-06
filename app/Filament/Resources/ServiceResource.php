<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\Schemas\ServiceForm;
use App\Filament\Resources\ServiceResource\Tables\ServicesTable;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ServiceResource\RelationManagers\ProductsRelationManager;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;


    protected static ?string $navigationGroup = 'Almacén';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $modelLabel = 'Servicio';
    protected static ?string $pluralModelLabel = 'Servicios';
    protected static ?string $navigationLabel = 'Servicios';

    public static function form(Form $form): Form
    {
        return ServiceForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['unit']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'unit.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->unit) {
            $details['Unit'] = $record->unit->name;
        }

        return $details;
    }
}
