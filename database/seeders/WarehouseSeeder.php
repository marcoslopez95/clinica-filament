<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Bodega',
                'description' => 'Almacén principal de suministros',
            ],
            [
                'name' => 'Clinica',
                'description' => 'Almacén de uso diario en clínica',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['name' => $warehouse['name']],
                $warehouse
            );
        }
    }
}
