<?php

namespace App\Enums;

use App\Models\Exam;
use App\Models\Product;
use App\Models\Room;
use App\Models\Service;

enum ResourceType: string
{
	case PRODUCT = Product::class;
	case ROOM = Room::class;
	case SERVICE = Service::class;
	case EXAM = Exam::class;

	public function getName(): string
	{
		return match ($this) {
			self::PRODUCT => 'Productos',
			self::ROOM => 'Habitaciones',
			self::SERVICE => 'Servicios',
			self::EXAM => 'Exámenes',
		};
	}
}
