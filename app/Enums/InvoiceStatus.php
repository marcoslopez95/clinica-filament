<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case OPEN = 'Abierta';
    case Closed = 'Cerrado';
    case Cancelled = 'Cancelado';
}
