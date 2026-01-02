<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $title = 'Descuentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('percentage')
                    ->label('Porcentaje (%)')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $percentage = (float) $state;
                        $set('amount', $total * ($percentage / 100));
                    }),
                Forms\Components\TextInput::make('amount')
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
                Forms\Components\TextInput::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Porcentaje')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
