<?php

namespace App\Filament\Resources\InvoiceReportResource\Pages;

use App\Filament\Resources\InvoiceReportResource;
use App\Exports\InvoicesExport;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class ListInvoiceReports extends ListRecords
{
    protected static string $resource = InvoiceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('exportExcel')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-document-plus')
                    ->form([
                        Toggle::make('is_detailed')
                            ->label('Detallado')
                            ->default(false),
                    ])
                    ->action(function (array $data) {
                        $records = $this->getFilteredTableQuery()->get();
                        return Excel::download(new InvoicesExport($records, $data['is_detailed'] ?? false), 'reporte-facturas-filtrado.xlsx');
                    }),

                Action::make('exportPdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->form([
                        Toggle::make('is_detailed')
                            ->label('Detallado')
                            ->default(false),
                    ])
                    ->action(function (array $data) {
                        $records = $this->getFilteredTableQuery()->get();
                        $mpdf = new Mpdf();
                        $html = view('pdf.invoices-report', [
                            'invoices' => $records,
                            'is_detailed' => $data['is_detailed'] ?? false
                        ])->render();
                        $mpdf->WriteHTML($html);
                        return response()->streamDownload(fn () => print($mpdf->Output('', 'S')), 'reporte-facturas-filtrado.pdf');
                    }),
            ])
            ->label('Exportar Todo')
            ->icon('heroicon-o-arrow-down-tray')
            ->button(),
        ];
    }
}
