<?php

namespace App\Filament\Resources\ExpenseResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Table;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Response;

class ExpensesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),

                TextColumn::make('price')
                    ->label('Precio')
                    ->sortable(),

                TextColumn::make('currency.name')
                    ->label('Moneda')
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),

                TextColumn::make('exchange')
                    ->label('Tasa de Cambio')
                    ->sortable(),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn(Table $table) => Excel::download(
                        new ExpensesExport($table->getFilteredTableQuery()->get()),
                        'reporte-gastos-' . now()->format('Y-m-d') . '.xlsx'
                    )),

                Action::make('exportPdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function (Table $table) {
                        $expenses = $table->getFilteredTableQuery()->get();
                        $html = view('pdf.expenses-report', ['expenses' => $expenses])->render();

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 10,
                            'margin_bottom' => 10,
                        ]);

                        $mpdf->WriteHTML($html);
                        return Response::streamDownload(
                            fn() => print($mpdf->Output('', 'S')),
                            'reporte-gastos-' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('expenses.bulk_delete')),

                    BulkAction::make('exportSelectedExcel')
                        ->label('Exportar Excel (Seleccionados)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(fn(Collection $records) => Excel::download(
                            new ExpensesExport($records),
                            'gastos-seleccionados-' . now()->format('Y-m-d') . '.xlsx'
                        )),
                    BulkAction::make('exportSelectedPdf')
                        ->label('Exportar PDF (Seleccionados)')
                        ->icon('heroicon-o-document-text')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $html = view('pdf.expenses-report', ['expenses' => $records])->render();
                            $mpdf = new Mpdf([
                                'mode' => 'utf-8',
                                'format' => 'A4',
                            ]);
                            $mpdf->WriteHTML($html);
                            return Response::streamDownload(
                                fn() => print($mpdf->Output('', 'S')),
                                'gastos-seleccionados-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),
                ]),
            ])
            ->filters([
                SelectFilter::make('expense_category_id')
                    ->relationship('category', 'name')
                    ->label('Categoría')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->label('Proveedor')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('currency_id')
                    ->relationship('currency', 'name')
                    ->label('Moneda'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query) => $query->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($query) => $query->whereDate('created_at', '<=', $data['until']));
                    })
                    ->label('Fecha'),
            ]);
    }
}
