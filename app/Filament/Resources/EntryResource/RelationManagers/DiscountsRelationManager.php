<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Filament\Resources\DiscountResource\Schemas\DiscountForm;
use App\Filament\Resources\DiscountResource\Tables\DiscountsTable;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $modelLabel = 'Descuento';
    protected static ?string $pluralModelLabel = 'Descuentos';
    protected static ?string $title = 'Descuentos';

    private function discountSchema(): array
    {
        $schema = DiscountForm::schema();

        foreach ($schema as $i => $component) {
            if (method_exists($component, 'getName')) {
                $name = $component->getName();

                if ($name === 'percentage') {
                    $schema[$i] = $component->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $percentage = (float) $state;
                        $set('amount', round($total * ($percentage / 100), 2));
                    })
                    ->formatStateUsing(fn($state) => $state !== null ? round($state, 2) : null)
                    ->live();
                }

                if ($name === 'amount') {
                    $schema[$i] = $component->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $total = (float) $this->getOwnerRecord()->total;
                        $amount = (float) $state;
                        if ($total > 0) {
                            $set('percentage', round(($amount / $total) * 100, 2));
                        }
                    })
                    ->formatStateUsing(fn($state) => $state !== null ? round($state, 2) : null)
                    ->live();
                }
            }
        }

        return $schema;
    }

    public function table(Table $table): Table
    {
        return DiscountsTable::table($table)
            ->recordTitleAttribute('description')

            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo ' . static::$modelLabel)
                    ->visible(fn (): bool => auth()->user()->can('entries.discounts.create'))
                    ->form($this->discountSchema())
                    ->action(function (array $data, $livewire): void {
                        $total = (float) $livewire->getOwnerRecord()->total;
                        $discountsSum = $livewire->getOwnerRecord()->discounts()->sum('amount') + (float) ($data['amount'] ?? 0);

                        if ($discountsSum > $total) {
                            Notification::make()
                                ->body("El monto de los descuentos no pueden superar al total de la factura")
                                ->danger()
                                ->send();
                            return;
                        }

                        $livewire->getOwnerRecord()->discounts()->create($data);
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('entries.discounts.edit.view'))
                    ->form($this->discountSchema())
                    ->action(function (Model $record, array $data, $livewire): void {
                        if (!auth()->user()->can('entries.discounts.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar este elemento')
                                ->danger()
                                ->send();

                            return;
                        }

                        $total = (float) $livewire->getOwnerRecord()->total;
                        $otherDiscounts = $livewire->getOwnerRecord()->discounts()
                            ->where('id', '!=', $record->id)
                            ->sum('amount');
                        $discountsSum = $otherDiscounts + (float) ($data['amount'] ?? 0);

                        if ($discountsSum > $total) {
                            Notification::make()
                                ->body("El monto de los descuentos no pueden superar al total de la factura")
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update($data);
                        $livewire->dispatch('refreshTotal');
                    }),
                RefreshTotalDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('entries.discounts.view');
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->discountSchema());
    }

}
