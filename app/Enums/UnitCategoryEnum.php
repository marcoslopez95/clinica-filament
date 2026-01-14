<?php

namespace App\Enums;

enum UnitCategoryEnum: string
{
    case LABORATORY = 'Laboratorio';
    case GENERAL = 'General';

    public function label(): string
    {
        return $this->value;
    }
}
