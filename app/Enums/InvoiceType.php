<?php

namespace App\Enums;

enum InvoiceType: string
{
	case DEFAULT = 'Default';
	case INVENTORY = 'Inventory';
	case LABORATORY = 'Laboratory';
}

