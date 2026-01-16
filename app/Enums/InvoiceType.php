<?php

namespace App\Enums;

enum InvoiceType: int
{
	case DEFAULT = 1;
	case INVENTORY = 2;
	case LABORATORY = 3;
	case COTIZACION = 4;
	case HOSPITALIZATION = 5;
	case CONSULT = 6;

	public function getName(): string
	{
		return match ($this) {
			self::DEFAULT => 'Quirofano',
			self::INVENTORY => 'Factura de Entrada',
			self::LABORATORY => 'Laboratorio',
			self::COTIZACION => 'Cotización',
			self::HOSPITALIZATION => 'Hospitalización',
			self::CONSULT => 'Consulta externa',
		};
	}
}
