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
	case TRANSFER = 7;
	case PHARMACY = 8;
	case CENTRAL_WAREHOUSE = 9;

	public function getName(): string
	{
		return match ($this) {
			self::DEFAULT => 'Quirofano',
			self::INVENTORY => 'Factura de Entrada',
			self::LABORATORY => 'Laboratorio',
			self::COTIZACION => 'Cotización',
			self::HOSPITALIZATION => 'Hospitalización',
			self::CONSULT => 'Consulta externa',
			self::TRANSFER => 'Transferencia de Inventario',
			self::PHARMACY => 'Farmacia',
			self::CENTRAL_WAREHOUSE => 'Almacén Central',
		};
	}

	public static function fromWarehouse(int $warehouseId): self
	{
		return match ($warehouseId) {
			1 => self::PHARMACY,
			2 => self::HOSPITALIZATION,
			3 => self::DEFAULT,
			4 => self::CONSULT,
			6 => self::CENTRAL_WAREHOUSE,
			default => self::TRANSFER,
		};
	}
}
