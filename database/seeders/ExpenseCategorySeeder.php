<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Suministros', 'description' => 'Gastos en material de oficina y consumibles'],
            ['name' => 'Servicios', 'description' => 'Pagos a proveedores de servicios'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::create($cat);
        }
    }
}
