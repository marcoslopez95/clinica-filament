<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Actions\RefreshTotalCreateAction;
use App\Filament\Actions\RefreshTotalEditAction;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Filament\Actions\RefreshTotalDeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\RefundResource\Schemas\RefundForm;

class RefundsRelationManager extends RelationManager
{
    protected static string $relationship = 'refunds';

    protected static ?string $title = 'Devoluciones';

    private function refundSchema(): array

    {
        $schema = RefundForm::schema();

        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([

                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago'),

                TextColumn::make('currency.name')
                    ->label('Moneda'),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money(fn($record) => $record->currency->code ?? 'USD'),

                TextColumn::make('exchange')
                    ->label('Tasa de Cambio'),
                    
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
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
                    RefreshTotalDeleteBulkAction::make(),
                ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->refundSchema());
    }
}
