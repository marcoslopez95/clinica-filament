<?php

namespace App\Filament\Filters;

use Filament\Tables\Filters\SelectFilter;
use App\Enums\InvoiceStatus;

class StatusFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('Status')
            ->options(InvoiceStatus::class)
            ->attribute('status');
    }
}
