<?php

namespace App\Filament\Resources\ProductMovementResource\Tables;

use App\Enums\InvoiceType;
use App\Models\InvoiceDetail;
use App\Models\Patient;
use App\Models\Supplier;
use App\Models\ProductCategory;
use App\Models\Warehouse;
use App\Exports\ProductMovementsExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductMovementsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content.name')
                    ->label('Producto')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph(
                            'content',
                            [\App\Models\Product::class],
                            fn (Builder $query) => $query->where('name', 'like', "%{$search}%")
                        );
                    })
                    ->sortable(),

                TextColumn::make('invoice.date')
                    ->label('Fecha del movimiento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->formatStateUsing(function (InvoiceDetail $record, $state) {
                        $prefix = (str_contains($record->description, 'Entrada') || $record->invoice->invoice_type === InvoiceType::INVENTORY)
                            ? '+' : '-';
                        return "{$prefix}{$state}";
                    })
                    ->color(fn (InvoiceDetail $record): string =>
                    (str_contains($record->description, 'Entrada') || $record->invoice->invoice_type === InvoiceType::INVENTORY) ? 'success' : 'danger'
                    )
                    ->sortable(),

                TextColumn::make('invoice.invoice_type')
                    ->label('Almacén/Tipo')
                    ->formatStateUsing(function ($record) {
                        $invoice = $record->invoice()->withoutGlobalScope('exclude_user_movements')->first();
                        return $invoice?->invoice_type?->getName() ?? 'N/A';
                    })
                    ->sortable(),

                TextColumn::make('invoice.invoiceable')
                    ->label('Paciente/Proveedor')
                    ->placeholder('N/A')
                    ->formatStateUsing(function (InvoiceDetail $record) {
                        $invoice = $record->invoice()->withoutGlobalScope('exclude_user_movements')->first();
                        $invoiceable = $invoice?->invoiceable;
                        if (!$invoiceable) return null;

                        if ($invoiceable instanceof Patient) {
                            return $invoiceable->fullName;
                        }

                        if ($invoiceable instanceof Supplier) {
                            return $invoiceable->name;
                        }

                        if ($invoiceable instanceof \App\Models\User) {
                            return $invoiceable->name;
                        }

                        return $invoiceable->name ?? $invoiceable->fullName ?? 'N/A';
                    }),

                TextColumn::make('price')
                    ->label('Precio del momento')
                    ->money(fn (InvoiceDetail $record) => $record->invoice->currency->code ?? 'USD')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('product_category')
                    ->label('Categoría de Producto')
                    ->options(fn () => ProductCategory::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHasMorph(
                            'content',
                            [\App\Models\Product::class],
                            fn (Builder $query) => $query->where('product_category_id', $data['value'])
                        );
                    }),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereHas('invoice', fn ($q) => $q->withoutGlobalScope('exclude_user_movements')->whereDate('date', '>=', $date)),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereHas('invoice', fn ($q) => $q->withoutGlobalScope('exclude_user_movements')->whereDate('date', '<=', $date)),
                            );
                    }),

                SelectFilter::make('patient')
                    ->label('Paciente')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Patient::where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->limit(50)->get()->pluck('fullName', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->fullName)
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('invoice', function (Builder $query) use ($data) {
                            $query->withoutGlobalScope('exclude_user_movements')
                                  ->where('invoiceable_id', $data['value'])
                                  ->where('invoiceable_type', Patient::class);
                        });
                    }),

                // Although the issue says "Almacén", Invoice doesn't have warehouse_id.
                // But the requirement says "Almacén que se hizo el descuento (tipo de factura)".
                // I'll add a filter for InvoiceType too if needed, but the user mentioned Almacén.
                // If they mean the physical Warehouse where the stock is stored (linked via Inventory),
                // we'd need to join or check the Inventory linked to the Product.
                // However, the description says "Almacén que se hizo el descuento, (tipo de factura)"
                // so it seems they equate warehouse with invoice type.
                // Let's add an Invoice Type filter.
                SelectFilter::make('invoice_type')
                    ->label('Almacén (Tipo de Factura)')
                    ->options(collect(InvoiceType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getName()]))
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->whereHas('invoice', fn (Builder $query) => $query->withoutGlobalScope('exclude_user_movements')->where('invoice_type', $data['value']));
                    }),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn ($livewire) => Excel::download(
                        new ProductMovementsExport($livewire->getFilteredTableQuery()->get()),
                        'movimientos-productos-' . now()->format('Y-m-d') . '.xlsx'
                    )),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('exportSelectedExcel')
                        ->label('Exportar Excel (Seleccionados)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(fn (Collection $records) => Excel::download(
                            new ProductMovementsExport($records),
                            'movimientos-productos-seleccionados-' . now()->format('Y-m-d') . '.xlsx'
                        )),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
