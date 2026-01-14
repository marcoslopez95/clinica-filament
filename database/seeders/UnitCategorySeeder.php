<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\UnitCategory;
use App\Enums\UnitCategoryEnum;

class UnitCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UnitCategoryEnum::cases() as $category) {
            UnitCategory::firstOrCreate([
                'name' => $category->value,
            ]);
        }
    }
}
