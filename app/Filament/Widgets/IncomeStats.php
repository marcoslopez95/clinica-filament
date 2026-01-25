<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class IncomeStats extends BaseWidget
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

        $stats = [];
        $paymentsByCurrency = Payment::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('currency_id, sum(amount) as total')
            ->groupBy('currency_id')
            ->with(['currency' => fn($q) => $q->withTrashed()])
            ->get();

        foreach ($paymentsByCurrency as $payment) {
            if (!$payment->currency) continue;
            $stats[] = Stat::make('Ingresos (' . $payment->currency->symbol . ')', number_format($payment->total, 2))
                ->description('Total recibido este mes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success');
        }

        return $stats;
    }
}
