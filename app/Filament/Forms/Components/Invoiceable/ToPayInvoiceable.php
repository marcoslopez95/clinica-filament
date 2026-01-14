<?php

namespace App\Filament\Forms\Components\Invoiceable;

use App\Models\Currency;
use App\Models\Invoice;
use App\Services\Helper;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\RelationManager;

class ToPayInvoiceable
{
    public static function make(): Section
    {
        return Section::make('Por Pagar')
            ->schema([
                TextInput::make('per_pay_invoiceable')
                    ->label('')
                    ->disabled()
                    ->dehydrated()
                    ->afterStateHydrated(function (TextInput $component, RelationManager $livewire, ?Model $record, Set $set) {
                        $currency = null;
                        if ($record && $record->currency) {
                            $currency = $record->currency;
                        } elseif ($livewire->ownerRecord && $livewire->ownerRecord->currency) {
                            $currency = $livewire->ownerRecord->currency;
                        }

                        if ($currency) {
                            $set('per_pay_invoiceable', self::recalculateBalance($currency, $livewire));
                        }
                    })
                    ->default(fn(RelationManager $livewire): string => self::recalculateBalance(
                        $livewire->ownerRecord->currency,
                        $livewire)
                    ),
            ]);
    }

    public static function recalculateBalance(Currency $currency,RelationManager $livewire): string
    {
        $balance = $currency->exchange * $livewire->ownerRecord->balance;
        return Helper::formatCurrency($balance, $currency);
    }
}
