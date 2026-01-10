<?php

namespace App\Enums;

enum InvoiceType: int
{
	case DEFAULT = 1;
	case INVENTORY = 2;
	case LABORATORY = 3;
	case COTIZACION = 4;
	case HOSPITALIZATION = 5;

	public function getName(): string
	{
		return match ($this) {
			self::DEFAULT => 'Factura',
			self::INVENTORY => 'Factura de Entrada',
			self::LABORATORY => 'Laboratorio',
			self::COTIZACION => 'Cotización',
			self::HOSPITALIZATION => 'Hozpitaliación',
		};
	}
}

