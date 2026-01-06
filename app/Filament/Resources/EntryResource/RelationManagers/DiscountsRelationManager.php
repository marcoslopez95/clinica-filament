<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use App\Filament\Actions\RefreshTotalCreateAction;
use App\Filament\Actions\RefreshTotalEditAction;
use App\Filament\Actions\RefreshTotalDeleteAction;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $title = 'Descuentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('percentage')
                    ->label('Porcentaje (%)')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $percentage = (float) $state;
                        $set('amount', $total * ($percentage / 100));
                    }),

                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $amount = (float) $state;
                        if ($total > 0) {
                            $set('percentage', ($amount / $total) * 100);
                        }
                    }),

                TextInput::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('percentage')
                    ->label('Porcentaje')
                    ->suffix('%'),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD'),
                TextColumn::make('description')
                    ->label('Descripción'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                RefreshTotalCreateAction::make(),
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
