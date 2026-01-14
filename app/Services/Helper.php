<?php

namespace App\Services;

use App\Models\Currency;

class Helper
{
    const DECIMALS = ',';
    const THOUSANDS = '.';
    public static function formatCurrency(float $amount, Currency $currency): string
    {
        return implode(' ',[
            number_format($amount, 2, self::DECIMALS, self::THOUSANDS),
            $currency->symbol
        ]);
    }

    public static function toFloatFromFormatCurrency(string $formatedAmount): float
    {
        $removeSymbol = explode(' ', $formatedAmount)[0];
        $onlyNumbers = str_replace([self::DECIMALS, self::THOUSANDS], '', $removeSymbol);
        return (float) $onlyNumbers;
    }
}
