<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpenseStats extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $stats = [];
        $expensesByCurrency = Expense::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('currency_id, sum(price) as total')
            ->groupBy('currency_id')
            ->with(['currency' => fn($q) => $q->withTrashed()])
            ->get();

        foreach ($expensesByCurrency as $expense) {
            if (!$expense->currency) continue;
            $stats[] = Stat::make('Gastos (' . $expense->currency->symbol . ')', number_format($expense->total, 2))
                ->description('Total gastos este mes')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('danger');
        }

        return $stats;
    }
}
