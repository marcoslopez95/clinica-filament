<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case OPEN = 'Por pagar';
    case CLOSED = 'Pagada';
    case CANCELLED = 'Cancelado';
    case PARTIAL = 'Pago parcial';
    case EXPIRED = 'Vencida';
}
