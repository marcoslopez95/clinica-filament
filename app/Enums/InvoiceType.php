<?php

namespace App\Enums;

enum InvoiceType: int
{
	case DEFAULT = 1;
	case INVENTORY = 2;
	case LABORATORY = 3;

	public function getName(): string
	{
		return match ($this) {
			self::DEFAULT => 'Factura',
			self::INVENTORY => 'Factura de Entrada',
			self::LABORATORY => 'Laboratorio'
		};
	}
}

