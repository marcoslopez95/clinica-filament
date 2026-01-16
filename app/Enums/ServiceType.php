<?php

namespace App\Enums;

enum ServiceCategory: int
{
    case DEFAULT = 1;
    case LABORATORY = 2;
    case QUOTATION = 3;
    case HOSPITALIZATION = 4;
    case CONSULT = 5;

    public function getName(): string
    {
        return match ($this) {
            self::DEFAULT => 'Quirofano',
            self::LABORATORY => 'Laboratorio',
            self::QUOTATION => 'Cotización',
            self::HOSPITALIZATION => 'Hospitalización y Emergencia',
            self::CONSULT => 'Consulta externa',
        };
    }
}
