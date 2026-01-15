<?php

namespace App\Filament\Resources\ReferenceValueResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;

use App\Enums\UnitCategoryEnum;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Unit;

class ReferenceValueForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label(false)
                ->placeholder('Nombre')
                ->unique(table: 'reference_values', column: 'name', ignoreRecord: true, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get, $livewire) {
                    $examId = $get('exam_id');

                    // Si no hay exam_id en el formulario actual (podría ser un RelationManager),
                    // intentamos obtenerlo del registro padre.
                    if (!$examId && $livewire instanceof \Filament\Resources\RelationManagers\RelationManager) {
                        $examId = $livewire->getOwnerRecord()->id;
                    }

                    return $rule
                        ->where('exam_id', $examId)
                        ->whereNull('deleted_at');
                })
                ->required(),

            TextInput::make('min_value')
                ->label(false)
                ->prefix('<')
                ->numeric()
                ->placeholder('mínimo')
                ->extraAttributes(['class' => 'text-center']),

            TextInput::make('max_value')
                ->label(false)
                ->prefix('>')
                ->numeric()
                ->placeholder('máximo')
                ->extraAttributes(['class' => 'text-center']),

            Select::make('unit_id')
                ->label(false)
                ->options(function () {
                    return Unit::whereHas('categories', function (Builder $query) {
                        $query->where('name', UnitCategoryEnum::LABORATORY->value);
                    })->pluck('name', 'id');
                })
                ->placeholder('Unidad')
                ->searchable()
                ->preload(),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([

            Select::make('exam_id')
                ->label('Examen')
                ->relationship('exam', 'name')
                ->required()
                ->preload(),

            ...self::schema(),

            \App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
