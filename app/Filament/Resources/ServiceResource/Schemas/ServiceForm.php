<?php

namespace App\Filament\Resources\ServiceResource\Schemas;

use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

use App\Enums\UnitCategoryEnum;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;

class ServiceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique('services', ignoreRecord: true),

                TextInput::make('buy_price')
                    ->label('Precio de Compra')
                    ->required()
                    ->numeric(),

                TextInput::make('sell_price')
                    ->label('Precio de Venta')
                    ->required()
                    ->numeric(),

                Select::make('unit_id')
                    ->label('Unidad')
                    ->required()
                    ->options(function () {
                        return Unit::whereHas('categories', function (Builder $query) {
                            $query->where('unit_categories.id', UnitCategoryEnum::PHARMACY->value);
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                Select::make('service_category_id')
                    ->label('Categoría')
                    ->required()
                    ->relationship('serviceCategory', 'name')
                    ->preload(),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
