<?php

namespace App\Filament\Resources\InvoiceReportResource\Tables;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Currency;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Room;
use App\Models\Supplier;
use App\Exports\InvoicesExport;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class InvoiceReportsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Nro')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Cliente/Paciente')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('dni')
                    ->label('DNI/RIF')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),

                TextColumn::make('invoice_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $state->getName())
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => $state->getName())
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        InvoiceStatus::OPEN => 'gray',
                        InvoiceStatus::CLOSED => 'success',
                        InvoiceStatus::CANCELLED => 'danger',
                        InvoiceStatus::PARTIAL => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money(fn ($record) => $record->currency?->code ?? 'USD')
                    ->sortable(),

                TextColumn::make('balance')
                    ->label('Saldo')
                    ->money(fn ($record) => $record->currency?->code ?? 'USD'),
            ])
            ->filters([
                SelectFilter::make('patient_id')
                    ->label('Paciente')
                    ->options(Patient::all()->mapWithKeys(fn ($p) => [$p->id => $p->last_name . ', ' . $p->first_name]))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('invoiceable_type', Patient::class)->where('invoiceable_id', $value)
                        );
                    }),

                SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->options(Supplier::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('invoiceable_type', Supplier::class)->where('invoiceable_id', $value)
                        );
                    }),

                SelectFilter::make('invoice_type')
                    ->label('Tipo de Factura')
                    ->options(collect(InvoiceType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getName()])),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getName()])),

                SelectFilter::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name'),

                TernaryFilter::make('is_expired')
                    ->label('Condición')
                    ->placeholder('Todas')
                    ->trueLabel('Vencida')
                    ->falseLabel('No Vencida'),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date));
                    }),

                Filter::make('habitaciones')
                    ->form([
                        Select::make('room_id')
                            ->label('Habitación')
                            ->options(Room::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['room_id'],
                            fn (Builder $query, $roomId): Builder => $query->whereHas('details', function ($query) use ($roomId) {
                                $query->where('content_type', Room::class)
                                    ->where('content_id', $roomId);
                            })
                        );
                    }),

                Filter::make('examenes')
                    ->form([
                        Select::make('exam_id')
                            ->label('Examen')
                            ->options(Exam::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['exam_id'],
                            fn (Builder $query, $examId): Builder => $query->whereHas('details', function ($query) use ($examId) {
                                $query->where('content_type', Exam::class)
                                    ->where('content_id', $examId);
                            })
                        );
                    }),

                Filter::make('productos')
                    ->form([
                        Select::make('product_id')
                            ->label('Producto')
                            ->options(Product::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['product_id'],
                            fn (Builder $query, $productId): Builder => $query->whereHas('details', function ($query) use ($productId) {
                                $query->where('content_type', Product::class)
                                    ->where('content_id', $productId);
                            })
                        );
                    }),
            ])
            ->actions([
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->form([
                        Toggle::make('is_detailed')
                            ->label('Detallado')
                            ->default(false),
                    ])
                    ->action(function ($record, array $data) {
                        $mpdf = new Mpdf();
                        $html = view('pdf.invoices-report', [
                            'invoices' => collect([$record]),
                            'is_detailed' => $data['is_detailed'] ?? false
                        ])->render();
                        $mpdf->WriteHTML($html);
                        return response()->streamDownload(fn () => print($mpdf->Output('', 'S')), "factura-{$record->invoice_number}.pdf");
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('exportExcel')
                        ->label('Exportar Excel')
                        ->icon('heroicon-o-document-plus')
                        ->form([
                            Toggle::make('is_detailed')
                                ->label('Detallado')
                                ->default(false),
                        ])
                        ->action(fn (Collection $records, array $data) => Excel::download(new InvoicesExport($records, $data['is_detailed'] ?? false), 'reporte-facturas.xlsx')),

                    BulkAction::make('exportPdf')
                        ->label('Exportar PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->form([
                            Toggle::make('is_detailed')
                                ->label('Detallado')
                                ->default(false),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $mpdf = new Mpdf();
                            $html = view('pdf.invoices-report', [
                                'invoices' => $records,
                                'is_detailed' => $data['is_detailed'] ?? false
                            ])->render();
                            $mpdf->WriteHTML($html);
                            return response()->streamDownload(fn () => print($mpdf->Output('', 'S')), 'reporte-facturas.pdf');
                        }),
                ]),
            ]);
    }
}
