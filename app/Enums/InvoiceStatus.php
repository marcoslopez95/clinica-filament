<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case OPEN = 'Abierta';
    case CLOSED = 'Cerrado';
    case CANCELLED = 'Cancelado';
}
