<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Enums\ServiceCategory as ServiceCategoryEnum;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => ServiceCategoryEnum::DEFAULT->value,
                'name' => ServiceCategoryEnum::DEFAULT->getName(),
            ],
            [
                'id' => ServiceCategoryEnum::LABORATORY->value,
                'name' => ServiceCategoryEnum::LABORATORY->getName(),
            ],
            [
                'id' => ServiceCategoryEnum::QUOTATION->value,
                'name' => ServiceCategoryEnum::QUOTATION->getName(),
            ],
            [
                'id' => ServiceCategoryEnum::HOSPITALIZATION->value,
                'name' => ServiceCategoryEnum::HOSPITALIZATION->getName(),
            ],
            [
                'id' => ServiceCategoryEnum::CONSULT->value,
                'name' => ServiceCategoryEnum::CONSULT->getName(),
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['id' => $category['id']],
                ['name' => $category['name']]
            );
        }
    }
}
