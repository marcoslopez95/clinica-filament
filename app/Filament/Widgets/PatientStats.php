<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PatientStats extends BaseWidget
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

        $patientsCount = Patient::whereHas('invoices', function ($query) use ($month, $year) {
            $query->whereMonth('date', $month)
                ->whereYear('date', $year);
        })->count();

        return [
            Stat::make('Pacientes Atendidos', $patientsCount)
                ->description('Pacientes con facturas este mes')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
