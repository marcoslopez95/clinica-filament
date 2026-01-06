<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Actions\RefreshTotalCreateAction;
use App\Filament\Actions\RefreshTotalEditAction;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Filament\Resources\DiscountResource\Schemas\DiscountForm;
use App\Filament\Resources\DiscountResource\Tables\DiscountsTable;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $title = 'Descuentos';

    public function form(Form $form): Form
    {
        $schema = DiscountForm::schema();

        foreach ($schema as $i => $component) {
            if (method_exists($component, 'getName')) {
                $name = $component->getName();

                if ($name === 'percentage') {
                    $schema[$i] = $component->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $percentage = (float) $state;
                        $set('amount', $total * ($percentage / 100));
                    });
                }

                if ($name === 'amount') {
                    $schema[$i] = $component->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $amount = (float) $state;
                        if ($total > 0) {
                            $set('percentage', ($amount / $total) * 100);
                        }
                    });
                }
            }
        }

        return $form->schema([
            ...$schema,
        ]);
    }

    public function table(Table $table): Table
    {
        return DiscountsTable::table($table)
            ->recordTitleAttribute('description')

            ->headerActions([
                RefreshTotalCreateAction::make()->label('Nuevo ' . static::$title),
            ])
            ->actions([
                RefreshTotalEditAction::make(),
                RefreshTotalDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
