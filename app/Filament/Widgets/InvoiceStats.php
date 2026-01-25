<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceType;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class InvoiceStats extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view');
    }

    protected function getStats(): array
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $invoiceStats = [];
        foreach (InvoiceType::cases() as $type) {
            $count = Invoice::where('invoice_type', $type->value)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->count();

            $invoiceStats[] = Stat::make('Facturas: ' . $type->getName(), $count)
                ->description('Cantidad en el mes')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success');
        }

        return $invoiceStats;
    }
}
