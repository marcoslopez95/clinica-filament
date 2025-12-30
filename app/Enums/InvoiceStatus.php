<?php

namespace App\Enums;

enum InvoiceStatus: int
{
    case OPEN = 1;
    case CLOSED = 2;
    case CANCELLED = 3;
    case PARTIAL = 4;

    public function getName(): string
    {
        return match ($this) {
            self::OPEN => 'Por pagar',
            self::CLOSED => 'Pagada',
            self::CANCELLED => 'Cancelado',
            self::PARTIAL => 'Pago parcial',
        };
    }
}
